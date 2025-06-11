<?php

namespace app\controllers\api;

/**
* This is the class for REST controller "InformePublicadoController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class InformePublicadoController extends \yii\rest\ActiveController
{
public $modelClass = 'app\models\InformePublicado';
}
