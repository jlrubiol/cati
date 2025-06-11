<?php

namespace app\controllers\api;

/**
* This is the class for REST controller "NotasPlanController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class NotasPlanController extends \yii\rest\ActiveController
{
public $modelClass = 'app\models\NotasPlan';
}
