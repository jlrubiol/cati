<?php

namespace app\controllers\gestion;

use app\models\CalendarioSearch;

/**
 * This is the class for controller "CalendarioController".
 */
class CalendarioController extends \app\controllers\base\CalendarioController
{
    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['gestor'],
                    ],
                ],
            ],
        ]);
    }

    public function actionIndex()
    {
        $searchModel = new CalendarioSearch();
        $dataProvider = $searchModel->search($_GET);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionView($id)
    {
        return $this->redirect('index');
    }
}
