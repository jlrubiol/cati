<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\Controller;
use \Da\User\Model\Profile;
use app\models\User;

class SamlController extends Controller
{
    // Remove CSRF protection
    public $enableCsrfValidation = false;

    public function actions()
    {
        $session = Yii::$app->session;

        return [
            'login' => [
                // This action will initiate the login process with the Identity Provider specified in the config file.
                'class' => 'asasmoyo\yii2saml\actions\LoginAction'
            ],
            'acs' => [
                // Assertion Consumer Service
                // This action will process the SAML response sent by the Identity Provider after succesful login.
                // You can register a callback to do some operation like reading the attributes sent by Identity Provider
                // and create a new user from those attributes.
                'class' => 'asasmoyo\yii2saml\actions\AcsAction',
                'successCallback' => [$this, 'callback'],
                'successUrl' => Url::to('@web/gestion/index'),
            ],
            'metadata' => [
                // This action will show metadata of your application in XML.
                'class' => 'asasmoyo\yii2saml\actions\MetadataAction'
            ],
            'logout' => [
                // This action will initiate the SingleLogout process with the Identity Provider.
                'class' => 'asasmoyo\yii2saml\actions\LogoutAction',
                'returnTo' => Url::to(['//site/index']),  // The target URL the user should be returned to after logout.
                'parameters' => [],  // Extra parameters to be added to the GET
                'nameId' => $session->get('nameId'),  // The NameID that will be set in the LogoutRequest.
                'sessionIndex' => $session->get('sessionIndex'),  //T he SessionIndex (taken from the SAML Response in the SSO process).
                'stay' => false,  // True if we want to stay (returns the url string) False to redirect
                'nameIdFormat' => null,  // The NameID Format will be set in the LogoutRequest.
                'nameIdNameQualifier' => $session->get('nameIdNameQualifier'),  // The NameID NameQualifier will be set in the LogoutRequest.
                'nameIdSPNameQualifier' => $session->get('nameIdSPNameQualifier'),
                'logoutIdP' => true,  // true if you want to logout on Identity Provider too
            ],
            'sls' => [
                // Single Logout Service
                // This action will process the SAML logout request/response sent by the Identity Provider.
                'class' => 'asasmoyo\yii2saml\actions\SlsAction',
                'successUrl' => Url::to(['//site/index']),
                'logoutIdP' => true,  // true if you want to logout on Identity Provider too
            ],
        ];  // NOTE: The acs and sls URLs should be set in the AssertionConsumerService and SingleLogoutService sections
            // of the metadata of this Service Provider in the IdP.
    }


    /**
     * @param array $attributes Attributes sent by the Identity Provider.
     */
    public function callback($attributes)
    {
        // SAML validation succeeded.  Let's login
        // Yii::info('SAML received attributes: ' . VarDumper::dumpAsString($attributes));

        # $nip = $attributes['attributes']['uid'][0];  # estudios sso (lord)
        $nip = $attributes['attributes']['urn:oid:0.9.2342.19200300.100.1.1'][0];  # sir sso
        $user = User::findByUsername($nip);

        // If it is the first time the user logs in, let's add it to the database.
        if (!$user) {
            $user = new User;
            $user->username = $nip;
            $user->email = "{$nip}@unizar.es";  // Defined as UNIQUE in the DB.
            # $user->password_hash = $attributes['attributes']['businessCategory'][0];  // estudios sso. Just because it is defined as NOT NULL in DB.
            $user->password_hash = $attributes['attributes']['urn:oid:2.5.4.15'][0];  // sir sso. Just because it is defined as NOT NULL in DB.
            $user->save();
        }

        // email and name may change.  Let's update them.
        $client = new \SoapClient(  # Requiere el paquete php-soap
            Yii::$app->params['WSDL_IDENTIDAD'],
            [
                'login' => Yii::$app->params['USER_IDENTIDAD'],
                'password' => Yii::$app->params['PASS_IDENTIDAD'],
                # 'trace' => 1,
            ]
        );
        $respuesta = $client->obtenIdentidad(['nip' => $nip]);
        $identidad = $respuesta->IdentidadResultado->identidad;
	// die(var_dump($identidad));

        $email_identidad = $identidad->correoPrincipal;
        if ($email_identidad) {
            $user->email = $email_identidad;
        }
        $profile = Profile::findOne(['user_id' => $user->id]);
        $profile->name = "{$identidad->nombre} {$identidad->primerApellido} {$identidad->segundoApellido}";
        $profile->gravatar_email = $user->email;
        $profile->save();

        /*
        $identidad = User::findIdentidadByNip($nip);
        $email_identidad = yii\helpers\ArrayHelper::getValue($identidad, 'CORREO_PRINCIPAL', null);
        if ($email_identidad) {
            $user->email = $email_identidad;
            $user->save();
        }

        $profile = Profile::findOne(['user_id' => $user->id]);
        $profile->name = sprintf(
            '%s %s %s',
            yii\helpers\ArrayHelper::getValue($identidad, 'NOMBRE', ''),
            yii\helpers\ArrayHelper::getValue($identidad, 'APELLIDO_1', ''),
            yii\helpers\ArrayHelper::getValue($identidad, 'APELLIDO_2', '')
        );  // $attributes['cn'][0];
        $profile->gravatar_email = $user->email;
        // TODO: Extender el profile para guardar el colectivo, nombres y apellidos por separado, etc.
        $profile->save();
        */

        Yii::$app->user->login($user);

        $session = Yii::$app->session;
        $session->set('saml_attributes', $attributes);
    }
}
