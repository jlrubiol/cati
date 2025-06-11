<?php
/**
 * /home/quique/Devel/cati/cati/runtime/giiant/49eb2de82346bc30092f584268252ed2.
 */

namespace app\controllers;

use app\models\Pagina;
use yii\data\ActiveDataProvider;

/**
 * This is the class for controller "PaginaController".
 */
class PaginaController extends \app\controllers\base\PaginaController
{
    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                // 'ruleConfig'
                'rules' => [
                    [
                        'actions' => ['ver'],
                        'allow' => true,
                    ], [
                        'actions' => ['editar', 'lista'],
                        'allow' => true,
                        'roles' => ['unidadCalidad', 'escuelaDoctorado'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Displays a single Pagina model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionVer($id)
    {
        return $this->render('ver', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     *  Muestra enlaces para editar cada una de las páginas.
     */
    public function actionLista()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Pagina::find(),
        ]);

        return $this->render('lista', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Pagina model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionEditar($id)
    {
        $model = $this->findModel($id);

        if ($model->load($_POST) && $model->save()) {
            return $this->redirect(['ver', 'id' => $model->id]);
        } else {
            return $this->render('editar', [
                'model' => $model,
            ]);
        }
    }
}
