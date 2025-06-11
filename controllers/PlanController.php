<?php
/**
 * /home/quique/Devel/cati/cati/runtime/giiant/49eb2de82346bc30092f584268252ed2.
 */

namespace app\controllers;

use app\models\Plan;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * This is the class for controller "PlanController".
 */
class PlanController extends \app\controllers\base\PlanController
{
    /** Ver notas en InformeController */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                // 'ruleConfig'
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['Admin'],
                    ],
                ],
            ],
        ];
    }
}
