<?php
// Inspired by <https://2am.blog/how-to-do-identity-impersonation-with-yii2/>

namespace app\controllers;

use Yii;
use app\models\User;
use yii\filters\AccessControl;
use yii\web\Controller;

class SuplantacionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'login', 'logout'],
                'rules' => [
                    [
                        'actions' => ['index', 'login',],
                        'allow' => true,
                        'roles' => ['unidadCalidad'],
                    ], [
                        'actions' => ['logout'],
                        'allow' => true,
                    ]
                ],
            ],
        ];
    }


    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionLogin()
    {
        if (Yii::$app->request->isPost) {
            $webUser = Yii::$app->getUser();
            $realIdentityId = $webUser->id;

            $username = Yii::$app->request->post('username');
            $user = User::findByUsername($username);

            if (!$user) {
                Yii::$app->session->addFlash(
                    'error',
                    Yii::t('gestion', "No se ha encontrado el usuario {$username}. ¿Ha iniciado sesión alguna vez?")
                );
                return $this->redirect(['//gestion']);
            }

            if ($realIdentityId != $user->id) {
                $webUser->login($user, $duration = 0);
                Yii::$app->session->set('real_identity_id', $realIdentityId);
            }
        }

        return $this->redirect(['//gestion/mis-estudios']);
    }


    public function actionLogout()
    {
        $webUser = Yii::$app->getUser();
        $realIdentityId = Yii::$app->session->get('real_identity_id');

        if (!empty($realIdentityId)) {
            $user = User::findOne($realIdentityId);

            $webUser->login($user, $duration = 0);
            Yii::$app->session->set('real_identity_id', null);
        }

        return $this->redirect(['/']);
    }
}