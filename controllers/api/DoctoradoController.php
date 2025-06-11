<?php

namespace app\controllers\api;

/**
* This is the class for REST controller "DoctoradoController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class DoctoradoController extends \yii\rest\ActiveController
{
public $modelClass = 'app\models\Doctorado';
}
