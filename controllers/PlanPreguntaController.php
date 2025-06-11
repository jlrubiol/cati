<?php
/**
 * Controlador de las preguntas de los planes de innovación y mejora.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */

namespace app\controllers;

use app\models\PaimOpcion;
use app\models\PlanPregunta;
use app\models\User;
use Yii;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * This is the class for controller "PlanPreguntaController".
 */
class PlanPreguntaController extends \app\controllers\base\PlanPreguntaController
{
    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'duplicar-preguntas',
                            'lista',
                            'ver',
                            'editar',
                            'crear',
                            'borrar',
                        ],
                        'allow' => true,
                        'roles' => ['unidadCalidad', 'escuelaDoctorado'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Copia las preguntas del plan de un año anterior a un nuevo año.
     */
    public function actionDuplicarPreguntas($anyo_viejo, $anyo_nuevo, $tipo)
    {
        // Si ya había preguntas del nuevo año, las borramos.
        $preguntas = PlanPregunta::find()
            ->where(['anyo' => $anyo_nuevo, 'tipo' => $tipo])
            ->all();
        foreach ($preguntas as $pregunta) {
            $traducciones = $pregunta->getTranslations()->all();
            array_map(function ($traduccion) {
                $traduccion->delete();
            }, $traducciones);
            $pregunta->delete();
        }

        $preguntas = PlanPregunta::find()
            ->where(['anyo' => $anyo_viejo, 'tipo' => $tipo])
            ->all();

        foreach ($preguntas as $pregunta) {
            $pregunta_nueva = new PlanPregunta([
                'anyo' => $anyo_nuevo,
                'apartado' => $pregunta->apartado,
                'atributos' => $pregunta->atributos,
                'tipo' => $tipo,
                'language' => $pregunta->language,
                'titulo' => $pregunta->titulo,
                'explicacion' => $pregunta->explicacion,
            ]);
            $pregunta_nueva->save();
        }

        $usuario = Yii::$app->user->identity;
        $nombre = $usuario->username;
        Yii::info("{$nombre} ha clonado las preguntas de los PAIM de {$tipo}.", 'gestion');

        // Si ya había opciones de respuesta del nuevo año, las borramos.
        $opciones = PaimOpcion::find()
            ->where(['anyo' => $anyo_nuevo, 'tipo_estudio' => $tipo])
            ->all();
        foreach ($opciones as $opcion) {
            $opcion->delete();
        }
        /*
        INSERT INTO paim_opcion (anyo, campo, tipo_estudio, valor)
          SELECT anyo+1, campo, tipo_estudio, valor
          FROM paim_opcion
          WHERE anyo = $anyo_viejo
          ORDER BY tipo_estudio DESC, campo, valor;
         */
        $opciones = PaimOpcion::find()
            ->where(['anyo' => $anyo_viejo, 'tipo_estudio' => $tipo])
            ->orderBy(['tipo_estudio' => SORT_DESC, 'campo' => SORT_ASC, 'valor' => SORT_ASC])
            ->all();
        foreach($opciones as $opcion) {
            $opcion_nueva = new PaimOpcion([
                'anyo' => $anyo_nuevo,
                'campo' => $opcion->campo,
                'tipo_estudio' => $tipo,
                'valor' => $opcion->valor,
            ]);
            $opcion_nueva->save();
        }

        return $this->redirect(Url::to([
            'plan-pregunta/lista',
            'anyo' => $anyo_nuevo,
            'tipo' => $tipo,
        ]));
    }

    /**
     * Muestra un listado de las preguntas de los planes de innovación y mejora.
     */
    public function actionLista($anyo, $tipo)
    {
        $exp = new Expression('CAST(SUBSTR(apartado, 1, 3) AS DECIMAL), apartado');
        $preguntas = PlanPregunta::find()
            ->where(['anyo' => $anyo, 'tipo' => $tipo])
            ->orderBy($exp)->all();

        if (!$preguntas) {
            Yii::$app->session->addFlash(
                'info',
                Yii::t('gestion', 'Por el momento no hay ningún apartado para el plan de este año.') . '<br>'
                    . Yii::t('gestion', 'Si lo desea, desde la página de Gestión puede clonar'
                    . ' los apartados del año anterior para no empezar desde cero.')
            );
        }

        return $this->render('lista', [
            'anyo' => $anyo,
            'preguntas' => $preguntas,
            'tipo' => $tipo,
        ]);
    }

    /**
     * Muestra una pregunta del plan de innovación y mejora.
     */
    public function actionVer($id)
    {
        $pregunta = PlanPregunta::findOne(['id' => $id]);
        if (!$pregunta) {
            throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese apartado.  ☹'));
        }

        return $this->render('ver', [
            'pregunta' => $pregunta,
        ]);
    }

    /**
     * Actualiza un apartado del plan de innovación y mejora.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionEditar($id)
    {
        $pregunta = PlanPregunta::findOne(['id' => $id]);
        if (!$pregunta) {
            throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese apartado.  ☹'));
        }

        if ($pregunta->load(Yii::$app->request->post())) {
            if ($pregunta->save()) {
                $usuario = Yii::$app->user->identity;
                $nombre = $usuario->username;
                Yii::info("$nombre ha editado una pregunta de un plan", 'gestion');

                return $this->redirect([
                    'ver',
                    'id' => $id,
                ]);
            }
        }

        return $this->render('editar', [
            'pregunta' => $pregunta,
        ]);
    }

    /**
     * Crea un nuevo apartado del plan de innovación y mejora.
     *
     * @return mixed
     */
    public function actionCrear($anyo, $tipo)
    {
        $pregunta = new PlanPregunta();

        try {
            if ($pregunta->load(Yii::$app->request->post())) {
                if ($pregunta->save()) {
                    $usuario = Yii::$app->user->identity;
                    $nombre = $usuario->username;
                    Yii::info("$nombre ha creado una pregunta de un plan de innovación y mejora", 'gestion');

                    return $this->redirect([
                        'ver',
                        'id' => $pregunta->id,
                    ]);
                }
            } elseif (!\Yii::$app->request->isPost) {
                $pregunta->load(Yii::$app->request->get());
            }
        } catch (Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            Yii::$app->getSession()->addFlash('error', $msg);
        }

        return $this->render('crear', [
            'anyo' => $anyo,
            'pregunta' => $pregunta,
            'tipo' => $tipo,
        ]);
    }

    /**
     * Borra un apartado del plan de innovación y mejora.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionBorrar($id)
    {
        try {
            $pregunta = PlanPregunta::findOne(['id' => $id]);
            if (!$pregunta) {
                throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese apartado.  ☹'));
            }
            $anyo = $pregunta->anyo;
            $tipo = $pregunta->tipo;
            $traducciones = $pregunta->getTranslations()->all();
            array_map(function ($traduccion) {
                $traduccion->delete();
            }, $traducciones);
            $pregunta->delete();
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            Yii::$app->getSession()->addFlash('error', $msg);

            return $this->redirect(Url::previous());
        }

        return $this->redirect(['lista', 'anyo' => $anyo, 'tipo' => $tipo]);
    }
}
