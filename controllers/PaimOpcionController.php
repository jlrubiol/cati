<?php

namespace app\controllers;

/**
* This is the class for controller "PaimOpcionController".
*/
class PaimOpcionController extends \app\controllers\base\PaimOpcionController
{
    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'rules' => [
                        [
                            'actions' => ['index', 'view', 'create', 'update', 'delete'],
                            'allow' => true,
                            'roles' => ['unidadCalidad'],
                        ],
                    ],
                ],
            ]
        );
    }
}
