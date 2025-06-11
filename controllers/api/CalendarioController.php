<?php

namespace app\controllers\api;

/**
* This is the class for REST controller "CalendarioController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class CalendarioController extends \yii\rest\ActiveController
{
public $modelClass = 'app\models\Calendario';
}
