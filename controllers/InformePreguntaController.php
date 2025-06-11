<?php
/**
 * Controlador de las preguntas de los informes de evaluación de la calidad.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */

namespace app\controllers;

use app\models\InformePregunta;
use app\models\User;
use Yii;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * This is the class for controller "InformePreguntaController".
 */
class InformePreguntaController extends \app\controllers\base\InformePreguntaController
{
    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'access' => [
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
     * Copia las preguntas del informe de un año anterior a un nuevo año.
     */
    public function actionDuplicarPreguntas($anyo_viejo, $anyo_nuevo, $tipo)
    {
        // Si ya había preguntas del nuevo año, las borramos.
        $preguntas = InformePregunta::find()
            ->where(['anyo' => $anyo_nuevo, 'tipo' => $tipo])
            ->all();
        foreach ($preguntas as $pregunta) {
            $traducciones = $pregunta->getTranslations()->all();
            array_map(function ($traduccion) {
                $traduccion->delete();
            }, $traducciones);
            $pregunta->delete();
        }

        $preguntas = InformePregunta::find()
            ->where(['anyo' => $anyo_viejo, 'tipo' => $tipo])
            ->all();

        foreach ($preguntas as $pregunta) {
            $pregunta_nueva = new InformePregunta([
                'anyo' => $anyo_nuevo,
                'apartado' => $pregunta->apartado,
                'editable' => $pregunta->editable,
                'tabla' => $pregunta->tabla,
                'tipo' => $pregunta->tipo,
                'language' => $pregunta->language,
                'titulo' => $pregunta->titulo,
                'info' => $pregunta->info,
                'explicacion' => $pregunta->explicacion,
                'texto_comun' => $pregunta->texto_comun,
            ]);
            $pregunta_nueva->save();
        }

        $usuario = Yii::$app->user->identity;
        $nombre = $usuario->username;
        Yii::info("{$nombre} ha clonado las preguntas de los informes de {$tipo}.", 'gestion');

        return $this->redirect(Url::to([
            'informe-pregunta/lista',
            'anyo' => $anyo_nuevo,
            'tipo' => $tipo,
        ]));
    }

    /**
     * Muestra un listado de las preguntas de los informes.
     */
    public function actionLista($anyo, $tipo)
    {
        URL::remember();
        $exp = new Expression('CAST(SUBSTR(apartado, 1, 3) AS DECIMAL), apartado');
        $preguntas = InformePregunta::find()
            ->where(['anyo' => $anyo, 'tipo' => $tipo])
            ->orderBy($exp)->all();

        if (!$preguntas) {
            Yii::$app->session->addFlash(
                'info',
                Yii::t('gestion', 'Por el momento no hay ningún apartado para el informe de este año.') . '<br>'
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
     * Muestra una pregunta.
     */
    public function actionVer($id)
    {
        $pregunta = InformePregunta::findOne(['id' => $id]);
        if (!$pregunta) {
            throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese apartado.  ☹'));
        }

        return $this->render('ver', [
            'pregunta' => $pregunta,
        ]);
    }

    /**
     * Actualiza un apartado del informe.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionEditar($id)
    {
        $pregunta = InformePregunta::findOne(['id' => $id]);
        if (!$pregunta) {
            throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese apartado.  ☹'));
        }

        if ($pregunta->load(Yii::$app->request->post())) {
            if ($pregunta->save()) {
                $usuario = Yii::$app->user->identity;
                $nombre = $usuario->username;
                Yii::info("$nombre ha editado una pregunta de un informe", 'gestion');

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
     * Crea un nuevo apartado del informe.
     *
     * @return mixed
     */
    public function actionCrear($anyo, $tipo)
    {
        $pregunta = new InformePregunta();

        try {
            if ($pregunta->load(Yii::$app->request->post())) {
                if ($pregunta->save()) {
                    $usuario = Yii::$app->user->identity;
                    $nombre = $usuario->username;
                    Yii::info("$nombre ha creado una pregunta de un informe", 'gestion');

                    return $this->redirect([
                        'ver',
                        'id' => $pregunta->id,
                    ]);
                }
            } elseif (!\Yii::$app->request->isPost) {
                // Obtiene el año y el tipo pasados en el URL
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
     * Borra un apartado del informe.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionBorrar($id)
    {
        try {
            $pregunta = InformePregunta::findOne(['id' => $id]);
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
