<?php

namespace app\controllers\api;

/**
* This is the class for REST controller "EquipoController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class EquipoController extends \yii\rest\ActiveController
{
public $modelClass = 'app\models\Equipo';
}
