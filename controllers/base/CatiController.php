<?php
/**
 * Controlador base de la aplicación.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 */

namespace app\controllers\base;

use Locale;
use Yii;
use yii\web\Controller;

class CatiController extends Controller
{
    /** Por omisión, deniega el acceso a todas las acciones, salvo al rol «admin». */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'ruleConfig' => [
                    'class' => \Da\User\Filter\AccessRuleFilter::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['Admin'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->getResponse()->redirect(['//saml/login']);
                    }
                    throw new \yii\web\ForbiddenHttpException(
                        Yii::t('app', 'No tiene permisos para acceder a esta página. 😨')
                    );
                },
            ],
        ];
    }

    /** Configura el idioma antes de ejecutar una acción */
    public function beforeAction($event)
    {
        $supported_languages = Yii::$app->params['languages'];
        $spanish_languages = Yii::$app->params['spanish_languages'];
        // Get the cookie collection and change target language
        $cookies = Yii::$app->request->cookies;
        $language = $cookies->getValue('language');

        // If the cookie doesn't contain a language value, look up the preferred language in the browser configuration.
        if (!$language) {
            $browser_languages = (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'es';
            $locale = Locale::acceptFromHttp($browser_languages);
            $language = strtok($locale, '_');
            // If the user language is a Spanish one, select Castilian.
            // If the user language is not amongst the supported languages, select English.
            if (in_array($language, $spanish_languages)) {
                $language = 'es';
            } elseif (!in_array($language, $supported_languages)) {
                $language = 'en';
            }

            // Set the cookie
            $cookies = Yii::$app->response->cookies;
            $cookies->add(new \yii\web\Cookie([
                'name' => 'language',
                'value' => $language,
            ]));
        }

        \Yii::$app->language = $language;

        return parent::beforeAction($event);
    }
}
