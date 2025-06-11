<?php

namespace app\controllers\api;

/**
* This is the class for REST controller "InformePreguntaController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class InformePreguntaController extends \yii\rest\ActiveController
{
public $modelClass = 'app\models\InformePregunta';
}
