<?php
/**
 * Controlador de los planes de innovación y mejora.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */

namespace app\controllers;

use app\models\Estudio;
use app\models\InformePublicado;
use app\models\PlanPregunta;
use app\models\PlanPublicado;
use app\models\PlanRespuesta;
# use mikehaertl\wkhtmlto\Pdf;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Yii;

class PlanMejoraController extends \app\controllers\base\CatiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $estudio_id = $request->post('PlanRespuesta')['estudio_id'] ?? $request->post('estudio_id');
        } else {
            // GET, HEAD, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH.
            $estudio_id = $request->get('estudio_id');
        }

        return \yii\helpers\ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'rules' => [
                        [
                            'actions' => ['crear', 'editar', 'borrar', 'add-accion', 'cerrar', 'completar', 'previsualizar', 'importar-accion'],
                            'allow' => true,
                            // Seguir bug #13598: https://github.com/yiisoft/yii2/issues/13598
                            'matchCallback' => function ($rule, $action) use ($estudio_id) {
                                $estudio = Estudio::getEstudio($estudio_id);
                                return (Yii::$app->user->can('editarPlan', ['estudio' => $estudio]) or ($estudio->esDoctorado() and Yii::$app->user->identity->esComisionDoctorado()));
                            },
                            'roles' => ['@'],
                        ],
                        [
                            'actions' => ['cargar-a-zaguan'],
                            'allow' => true,
                            'roles' => ['escuelaDoctorado', 'unidadCalidad'],
                        ],
                        [
                            'actions' => ['marc-xml', 'marc-xml-doct', 'ver'],
                            'allow' => true,
                        ],
                    ],
                ], /*
                'verbs' => [
                    'class' => \yii\filters\VerbFilter::className(),
                    'actions' => [
                        'crear' => ['POST'],
                    ],
                ], */
            ]
        );
    }

    /**
     * Muestra el plan de anual innovación y mejora de un estudio y año.
     */
    public function actionVer($estudio_id, $anyo, $completado = false)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $language = Yii::$app->language;
        $plan_publicado = PlanPublicado::find()
            ->where(['estudio_id' => $estudio_id, 'anyo' => $anyo, 'language' => $language])->one();

        $nueva_version = isset($plan_publicado) ? $plan_publicado->version + 1 : 1;
        $nombre_nueva_version = PlanPublicado::getNombreVersion($estudio->getTipoEstudio(), $nueva_version);

        // El campo apartado es una cadena, por lo que se ordena alfabéticamente
        // y 10 va después de 1 en lugar de después de 9.
        // Con esta expresión convertimos a tipo numérico los primeros 3 caracteres
        // y ordenamos correctamente.  En MS SQLServer usar SUBSTRING().
        $exp = new Expression('CAST(SUBSTR(apartado, 1, 3) AS DECIMAL), apartado');

        $preguntas = PlanPregunta::find()
            ->where(['anyo' => $anyo, 'tipo' => $estudio->getTipoEstudio()])
            ->orderBy($exp)
            ->all();
        if (!$preguntas) {
            throw new HttpException(
                404,
                Yii::t('cati', 'No se han encontrado las preguntas del plan de mejora para este año.  ☹')
            );
        }

        $respuestas = PlanRespuesta::find()
            ->where(['estudio_id' => $estudio_id, 'anyo' => $anyo])
            ->orderBy($exp)
            ->all();

        $respuestas2 = [];
        foreach ($respuestas as $respuesta) {
            $respuestas2[$respuesta->plan_pregunta_id][] = $respuesta;
        }

        $vista = 'ver';
        if ($completado) {
            $vista = 'ver-completado';
        }
        return $this->render(
            $vista,
            [
                'anyo' => intval($anyo),
                'estudio' => $estudio,
                'nombre_nueva_version' => $nombre_nueva_version,
                'nueva_version' => $nueva_version,
                'preguntas' => $preguntas,
                'respuestas' => $respuestas2,
            ]
        );
    }

    /**
     * Creates a new PlanRespuesta model.
     * If creation is successful, the browser will be redirected to the 'ver' page.
     *
     * @return mixed
     */
    public function actionCrear($estudio_id, $plan_pregunta_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $language = Yii::$app->language;
        $plan_publicado = PlanPublicado::find()
            ->where(['estudio_id' => $estudio_id, 'language' => $language])->one();

        if ($plan_publicado && $plan_publicado->version >= 1) {
            // La 2a y 3a versión la modifica y aprueba el presidente de la comisión de garantía de la calidad
            $usuario = Yii::$app->user->identity;
            $presidentes = $estudio->getNipPresidentesGarantiaYDelegados();
            $esPresidente = in_array($usuario->username, $presidentes);
            if (!($esPresidente or Yii::$app->user->can('editarPlan'))) {
                throw new ForbiddenHttpException(Yii::t('cati', 'No tiene permisos para modificar esta versión del PAIM.  😱'));
            }
        }

        $respuesta = new PlanRespuesta();

        try {
            if ($respuesta->load(Yii::$app->request->post()) and $respuesta->save()) {
                $nombre_usuario = Yii::$app->user->identity->username;
                Yii::info(
                    "$nombre_usuario} ha creado un nuevo registro en el plan de mejora"
                        . " del estudio {$respuesta->estudio_id}.",
                    'coordinadores'
                );

                return $this->redirect(
                    [
                        'plan-mejora/ver',
                        'estudio_id' => $respuesta->estudio_id,
                        'anyo' => $respuesta->anyo,
                    ]
                );
            } elseif (!\Yii::$app->request->isPost) {
                // $respuesta->load(Yii::$app->request->get());
                $respuesta->attributes = Yii::$app->request->get();
            }
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            // Yii::$app->getSession()->addFlash('error', $msg);
            $respuesta->addError('_exception', $msg);
        }

        $estudio = Estudio::getEstudio($estudio_id);

        $pregunta = PlanPregunta::findOne(['id' => $plan_pregunta_id]);
        if (!$pregunta) {
            throw new HttpException(404, Yii::t('cati', 'No se ha encontrado esa pregunta.  ☹'));
        }

        return $this->render(
            'crear',
            [
                'estudio' => $estudio,
                'pregunta' => $pregunta,
                'respuesta' => $respuesta,
            ]
        );
    }

    /**
     * Updates an existing PlanRespuesta model.
     * If update is successful, the browser will be redirected to the 'ver' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionEditar($estudio_id, $id)
    {
        $respuesta = PlanRespuesta::findOne(['id' => $id]);
        if (!$respuesta) {
            throw new HttpException(404, Yii::t('cati', 'The requested page does not exist.'));
        }
        // $estudio_id se usa para comprobar que el usuario tiene permisos para acceder a este método.
        if ($respuesta->estudio_id != $estudio_id) {
            throw new ServerErrorHttpException(
                Yii::t(
                    'cati',
                    'Datos inconsistentes.  El estudio del registro no coincide con el de la petición.'
                )
            );
        }

        $estudio = Estudio::getEstudio($estudio_id);
        $language = Yii::$app->language;
        $plan_publicado = PlanPublicado::find()
            ->where(['estudio_id' => $estudio_id, 'language' => $language])->one();

        if ($plan_publicado && $plan_publicado->version >= 1 && !$estudio->esDoctorado()) {
            // La 2a y 3a versión la modifica y aprueba el presidente de la comisión de garantía de la calidad
            // En Doctorado, el coordinador modifica y cierra también el PAIM definitivo.
            $usuario = Yii::$app->user->identity;
            $presidentes = $estudio->getNipPresidentesGarantiaYDelegados();
            $esPresidente = in_array($usuario->username, $presidentes);
            if (!($esPresidente or Yii::$app->user->can('editarPlan'))) {
                throw new ForbiddenHttpException(Yii::t('cati', 'No tiene permisos para modificar esta versión del PAIM.  😱'));
            }
        }

        if ($respuesta->load(Yii::$app->request->post()) && $respuesta->save()) {
            $nombre_usuario = Yii::$app->user->identity->username;
            Yii::info(
                "$nombre_usuario ha editado el registro $id del plan de mejora del estudio {$respuesta->estudio_id}",
                'coordinadores'
            );

            return $this->redirect(
                [
                    'ver',
                    'estudio_id' => $respuesta->estudio_id,
                    'anyo' => $respuesta->anyo,
                ]
            );
        } else {
            return $this->render(
                'editar',
                [
                    'respuesta' => $respuesta,
                    'estudio' => Estudio::getEstudio($estudio_id),
                ]
            );
        }
    }

    /**
     * Crea una nueva acción con la misma necesidad, ámbito y objetivo de otra acción.
     */
    public function actionAddAccion($estudio_id, $id)
    {
        $respuesta = PlanRespuesta::findOne(['id' => $id]);
        if (!$respuesta) {
            throw new HttpException(404, Yii::t('cati', 'The requested page does not exist.'));
        }
        // $estudio_id se usa para comprobar que el usuario tiene permisos para acceder a este método.
        if ($respuesta->estudio_id != $estudio_id) {
            throw new ServerErrorHttpException(
                Yii::t(
                    'cati',
                    'Datos inconsistentes.  El estudio del registro no coincide con el de la petición.'
                )
            );
        }

        $nuevaRespuesta = new PlanRespuesta();
        $nuevaRespuesta->estudio_id = $respuesta->estudio_id;
        $nuevaRespuesta->anyo = $respuesta->anyo;
        $nuevaRespuesta->plan_pregunta_id = $respuesta->plan_pregunta_id;
        $nuevaRespuesta->apartado = $respuesta->apartado;
        $nuevaRespuesta->estudio_id_nk = $respuesta->estudio_id_nk;
        $nuevaRespuesta->ambito_id= $respuesta->ambito_id;
        $nuevaRespuesta->necesidad_detectada= $respuesta->necesidad_detectada;
        $nuevaRespuesta->objetivo = $respuesta->objetivo;
        try {
            $nuevaRespuesta->save();
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            // Yii::$app->getSession()->addFlash('error', $msg);
            $respuesta->addError('_exception', $msg);
        }

        return $this->redirect(
            [
                'editar',
                'estudio_id' => $nuevaRespuesta->estudio_id,
                'id' => $nuevaRespuesta->id,
            ]
        );
    }

    /**
     * Deletes an existing PlanRespuesta model.
     * If deletion is successful, the browser will be redirected to the 'ver' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionBorrar($estudio_id, $id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $language = Yii::$app->language;
        $plan_publicado = PlanPublicado::find()
            ->where(['estudio_id' => $estudio_id, 'language' => $language])->one();

        if ($plan_publicado && $plan_publicado->version >= 1 && !$estudio->esDoctorado()) {
            // La 2a y 3a versión la modifica y aprueba el presidente de la comisión de garantía de la calidad
            // En Doctorado, el coordinador modifica y cierra también el PAIM definitivo.
            $usuario = Yii::$app->user->identity;
            $presidentes = $estudio->getNipPresidentesGarantiaYDelegados();
            $esPresidente = in_array($usuario->username, $presidentes);
            if (!($esPresidente or Yii::$app->user->can('editarPlan'))) {
                throw new ForbiddenHttpException(Yii::t('cati', 'No tiene permisos para modificar esta versión del PAIM.  😱'));
            }
        }

        try {
            $respuesta = PlanRespuesta::findOne(['id' => $id]);
            if (!$respuesta) {
                throw new HttpException(404, Yii::t('cati', 'The requested page does not exist.'));
            }
            // $estudio_id se usa para comprobar que el usuario tiene permisos para acceder a este método.
            if ($respuesta->estudio_id != $estudio_id) {
                throw new ServerErrorHttpException(
                    Yii::t(
                        'cati',
                        'Datos inconsistentes.  El estudio del registro no coincide con el de la petición.'
                    )
                );
            }
            $traducciones = $respuesta->getTranslations()->all();
            array_map(
                function ($traduccion) {
                    $traduccion->delete();
                },
                $traducciones
            );
            $respuesta->delete();

            $nombre_usuario = Yii::$app->user->identity->username;
            Yii::info(
                "$nombre_usuario ha borrado el registro $id del plan de mejora del estudio {$respuesta->estudio_id}",
                'coordinadores'
            );
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            Yii::$app->getSession()->addFlash('error', $msg);

            return $this->redirect(
                [
                    'ver',
                    'estudio_id' => $respuesta->estudio_id,
                    'anyo' => $respuesta->anyo,
                ]
            );
        }

        return $this->redirect(
            [
                'ver',
                'estudio_id' => $respuesta->estudio_id,
                'anyo' => $respuesta->anyo,
            ]
        );
    }

    public function actionCerrar($estudio_id, $anyo)
    {
        // Establecer las variables
        $estudio = Estudio::getEstudio($estudio_id);
        $language = Yii::$app->language;

        $informePublicado = InformePublicado::find()
            ->where(['estudio_id' => $estudio_id, 'anyo' => $anyo, 'language' => $language])->one();
        if (!$informePublicado) {
            $mensaje = "Debe cerrar el IEC antes que el PAIM.\n";
            Yii::$app->session->addFlash('error', Yii::t('app', $mensaje));

            return $this->redirect([$estudio->getMetodoVerInforme(), 'estudio_id' => $estudio_id, 'anyo' => $anyo]);
        }

        $plan_publicado = PlanPublicado::find()
            ->where(['estudio_id' => $estudio_id, 'anyo' => $anyo, 'language' => $language])->one();

        if (!$plan_publicado) {
            $plan_publicado = new PlanPublicado(
                [
                    'estudio_id' => $estudio_id,
                    'anyo' => intval($anyo),
                    'language' => $language,
                    'version' => 0,
                    'estudio_id_nk' => $estudio->id_nk,
                ]
            );
        }

        if ($plan_publicado->version >= 1) {
            if (!$estudio->esDoctorado()) {  // Grado-Master
                // La 2a y 3a versión la modifica y aprueba el presidente de la comisión de garantía de la calidad
                $usuario = Yii::$app->user->identity;
                $presidentes = $estudio->getNipPresidentesGarantiaYDelegados();
                $esPresidente = in_array($usuario->username, $presidentes);
                if (!($esPresidente or Yii::$app->user->can('editarPlan'))) {
                    throw new ForbiddenHttpException(Yii::t('cati', 'No tiene permisos para cerrar esta versión del PAIM.  😱'));
                }
            } else {  // Doctorado
                /*
                1. El coordinador entra a la versión 0, añade acciones y al cerrarla genera la versión 1 (versión provisional)
                2. La Comisión de Doctorado revisa las acciones y aprueba los PAIM que son ok->
                   los cerramos en el Área de calidad y mejora (también deben poder hacerlo en la Escuela de Doctorado),
                   y generamos la V2, que es el PAIM definitivo.
                3. Los PAIM que hay que modificar vuelven a abrirse por el Área de Calidad (vuelven a la versión 0)
                   para que los coordinadores los modifiquen y los vuelvan a cerrar
                4. La Comisión de Doctorado vuelve a revisar y nos da el ok y somos el ACM quienes los cerramos
                   (o, si se da la necesidad, la EDUZ)
                */
                $usuario = Yii::$app->user;
                if (!($usuario->can('unidadCalidad') or $usuario->can('escuelaDoctorado'))) {
                    throw new ForbiddenHttpException(Yii::t('cati', 'No tiene permisos para cerrar esta versión del PAIM.  😱'));
                }
            }
        }

        $version_maxima = $plan_publicado->getVersionMaxima();
        if ($plan_publicado->version >= $version_maxima) {
            throw new ServerErrorHttpException('Este plan de innovación y mejora ya está en su versión final. 😨');
        }
        $nueva_version = $plan_publicado->version + 1;
        $nombre_nueva_version = PlanPublicado::getNombreVersion($estudio->getTipoEstudio(), $nueva_version);

        if ($nueva_version === $version_maxima) {
            # Para cerrar la versión definitiva, es obligatorio haber introducido la fecha de aprobación
            $preguntas_obligatorias = PlanPregunta::find()
                ->where(['anyo' => $anyo, 'tipo' => $estudio->getTipoEstudio(), 'oblig_def' => true])
                ->all();
            foreach ($preguntas_obligatorias as $pregunta) {
                $respuesta = PlanRespuesta::find()
                    ->where(['estudio_id' => $estudio_id, 'anyo' => $anyo])
                    ->andWhere(['plan_pregunta_id' => $pregunta->id])
                    ->all();
                if (!$respuesta) {
                    $mensaje = "Es obligatorio responder el apartado {$pregunta->apartado}.\n";
                    Yii::$app->session->addFlash('error', Yii::t('app', $mensaje));
                    return $this->redirect(['plan-mejora/ver', 'estudio_id' => $estudio_id, 'anyo' => $anyo]);
                }
            }
            # No se podrá cerrar el PAIM definitivo si no se ha cerrado antes el IEC definitivo
            # (Email de sdcalidad del 2023-12-04)
            $version_maxima_informe = $informePublicado->getVersionMaxima();
            if ($informePublicado->version < $version_maxima_informe) {
                $mensaje = "Debe cerrar la versión definitiva del IEC antes que el PAIM definitivo.\n";
                Yii::$app->session->addFlash('error', Yii::t('app', $mensaje));
                return $this->redirect([$estudio->getMetodoVerInforme(), 'estudio_id' => $estudio_id, 'anyo' => $anyo]);
            }
        }

        $plan = PlanMejoraController::generarPdfPlan($estudio_id, $anyo);
        $ruta_plan = $plan['ruta'];
        $url_pdf_plan = $plan['url'];

        // Guardar número de versión publicada
        $plan_publicado->version = $nueva_version;
        $plan_publicado->save();

        $nombre_usuario = Yii::$app->user->identity->username;
        Yii::info(
            "{$nombre_usuario} ha cerrado la versión {$nueva_version} del plan de mejora del estudio {$estudio_id}.",
            'coordinadores'
        );

        /*
         * Enviar mensajes de correo electrónico
         */
        $destinatarios = [];

        if ($estudio->esGradoOMaster()) {
            /*
            * La versión 1 se envía a:
            *  - los coordinadores del estudio en los centros,
            *  - los presidentes de las comisiones de garantía de la calidad.
            * La versión 2 se envía además a:
            *  - Dirección de los centros responsables del título,
            *  - Expertos del rector/CIFICE
            */
            $coordinadores = $estudio->getCoordinadoresYDelegados();
            $presidentes = $estudio->getPresidentesGarantiaYDelegados();
            $destinatarios = array_merge($coordinadores, $presidentes);

            if ($nueva_version >= 2) {
                $decanos = $estudio->getDecanos();
                $expertos = $estudio->getExpertosRector();
                $destinatarios = array_merge($destinatarios, $decanos, $expertos);

                if (3 == $nueva_version) {
                    # $destinatarios[] = Yii::$app->params['mailvr'];  # vicerrector de política académica
                }
            }
        } elseif ($estudio->esDoctorado()) {
            $destinatarios = [
                $estudio->plans[0]->email_coordinador,
                # Yii::$app->params['diredoc'],
                # Yii::$app->params['presiDoct'],
            ];
        }

        $destinatarios = array_unique($destinatarios);
        $this->enviarCorreo($destinatarios, $estudio, $anyo, $nombre_nueva_version, $nueva_version, $url_pdf_plan, $ruta_plan);

        // Redirigir al PDF
        return $this->redirect($url_pdf_plan);
    }

    /**
     * Actualiza los valores alcanzados/observaciones/estado de una acción.
     * TODO: Controlar cuando se muestran los botones para completar: solamente al coordinador, y solamente si el IEC está abierto
     */
    public function actionCompletar($estudio_id, $id)
    {
        $respuesta = PlanRespuesta::findOne(['id' => $id]);
        if (!$respuesta) {
            throw new HttpException(404, Yii::t('cati', 'The requested page does not exist.'));
        }

        // $estudio_id se usa para comprobar que el usuario tiene permisos para acceder a este método.
        if ($respuesta->estudio_id != $estudio_id) {
            throw new ServerErrorHttpException(
                Yii::t(
                    'cati',
                    'Datos inconsistentes.  El estudio del registro no coincide con el de la petición.'
                )
            );
        }

        $estudio = Estudio::getEstudio($estudio_id);
        $language = Yii::$app->language;
        $plan_publicado = PlanPublicado::find()
            ->where(['estudio_id' => $estudio_id, 'language' => $language])->one();

        if ($respuesta->load(Yii::$app->request->post()) && $respuesta->save()) {
            $nombre_usuario = Yii::$app->user->identity->username;
            Yii::info(
                "$nombre_usuario ha editado el registro $id del plan de mejora del estudio {$respuesta->estudio_id}",
                'coordinadores'
            );
            return $this->redirect(Url::previous());  # Usar Url::remember() en la página anterior
            /*
            return $this->redirect(
                [
                    'informe/ver',
                    'estudio_id' => $respuesta->estudio_id,  # XXX
                    'anyo' => $respuesta->anyo + 1,
                ]
            );
            */
        } else {
            return $this->render(
                'completar',
                [
                    'respuesta' => $respuesta,
                    'estudio' => Estudio::getEstudio($estudio_id),
                ]
            );
        }
    }

    public static function generarPdfPlan($estudio_id, $anyo, $completado = false)
    {
        $language = Yii::$app->language;
        $estudio = Estudio::getEstudio($estudio_id);

        $plan_publicado = PlanPublicado::find()
            ->where(['estudio_id' => $estudio_id, 'anyo' => $anyo, 'language' => $language])->one();
        $nueva_version = $plan_publicado ? $plan_publicado->version + 1 : 1;

        $dir_planes = Yii::getAlias('@webroot') . '/pdf/planes-mejora';
        $dir_planes_anyo = "{$dir_planes}/{$anyo}";
        if (!is_dir($dir_planes_anyo)) {
            mkdir($dir_planes_anyo);
            copy("{$dir_planes}/index.html", "{$dir_planes_anyo}/index.html");
        }

        $nombre_fichero = "plan-{$language}-{$estudio->id_nk}-v{$nueva_version}.pdf";
        $ruta_fichero = "{$dir_planes_anyo}/{$nombre_fichero}";
        $url_pdf_plan = Url::base(true) . "/pdf/planes-mejora/{$anyo}/{$nombre_fichero}";
        $url_plan = Url::to(
            [
                'plan-mejora/ver',
                'estudio_id' => $estudio_id,
                'anyo' => $anyo,
                # Al redactar el IEC, se completa el PAIM del año anterior con los valores alcanzados, etc.
                'completado' => $completado,
            ],
            true
        );  // true: Absolute URL

        # TODO Considerar <https://github.com/pontedilana/php-weasyprint>
        # $command = "/usr/bin/weasyprint --debug \"{$url_plan}\" {$ruta_fichero}";
        # exec($command, $output, $retval);
        $process = new Process(['/usr/bin/weasyprint', $url_plan, $ruta_fichero]);
        $process->setTimeout(60);  // timeout in seconds
        $process->run();
        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        /*
        $ruta_galletas = tempnam($dir_planes_anyo, $language);

        // Generar una cookie-jar para establecer el idioma
        $pdf = new Pdf(['cookie-jar' => $ruta_galletas]);
        $pdf->addPage(Url::to(['language/set2', 'language' => $language], true));  // true: URL absoluta.
        $pdf->setOptions(
            [
                'binary' => Yii::$app->params['wkhtmltopdf'],
                // 'inputArg',
                'print-media-type',
            ]
        );
        if (!$pdf->saveAs('/dev/null')) {
            throw new ServerErrorHttpException($pdf->getError());
        }

        // Generar el PDF
        $pdf = new Pdf(
            [
                'cookie-jar' => $ruta_galletas,
                'binary' => Yii::$app->params['wkhtmltopdf'],
                'orientation' => 'Landscape',
                'print-media-type',
            ]
        );

        $pdf->addPage(
            Url::to(
                [
                    'plan-mejora/ver',
                    'estudio_id' => $estudio_id,
                    'anyo' => $anyo,
                    # Al redactar el IEC, se completa el PAIM del año anterior con los valores alcanzados, etc.
                    'completado' => $completado
                ],
                true
            )
        );  // true: Absolute URL
        if (!$pdf->saveAs($ruta_fichero)) {
            throw new ServerErrorHttpException($pdf->getError());
        }

        unlink($ruta_galletas);
        */

        return ['ruta' => $ruta_fichero, 'url' => $url_pdf_plan];
    }

    private function enviarCorreo($destinatarios, $estudio, $anyo, $nombre_nueva_version, $nueva_version, $url_pdf, $ruta_fichero)
    {
        if ($estudio->esDoctorado()) {
            $plantilla = 'plan-doct-cerrado';  // @app/mail/plan-doct-cerrado.php
        } else {
            $plantilla = 'plan-cerrado';  // @app/mail/plan-cerrado.php
        }

        if (!empty($destinatarios)) {
            $mensaje = Yii::$app->mailer->compose(
                "{$plantilla}-v{$nueva_version}",  // @app/mail/plan-cerrado-v2.php
                [
                    'estudio' => $estudio,
                    'anyo' => $anyo,
                    'nombre_nueva_version' => $nombre_nueva_version,
                    'version' => $nueva_version,
                    'url_pdf' => $url_pdf,
                ]
            )->setFrom([Yii::$app->params['adminEmail'] => 'Robot Estudios'])
                ->setTo($destinatarios)
                ->setSubject("Publicado plan de mejora v$nueva_version");
            // ->setTextBody($texto)
            // ->setHtmlBody('<b>HTML content</b>');

            $mensaje->attach($ruta_fichero);
            $mensaje->send();
        }
    }

    public function actionPrevisualizar($estudio_id, $anyo, $completado = false)
    {
        $plan = PlanMejoraController::generarPdfPlan($estudio_id, $anyo, $completado);
        // Redirigir al PDF
        return $this->redirect($plan['url']);
    }

    /**
     * Genera un MarcXML para exportar los planes de mejora de un año.
     *
     * Puede generar el MarcXML de un grado/máster dado o de todos.
     */
    public function actionMarcXml($anyo, $estudio_id = null)
    {
        Yii::$app->language = 'es';
        $dir_paim = Yii::getAlias('@webroot') . "/pdf/planes-mejora/{$anyo}";
        if ($estudio_id) {
            $estudios = [Estudio::getEstudio($estudio_id)];
        } else {
            $estudios = Estudio::find()
                ->where(['anyo_academico' => $anyo])
                ->andWhere(
                    [
                        'in',
                        'tipoEstudio_id',
                        [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID],
                    ]
                )->all();
        }
        $estudios = array_filter(
            $estudios,
            function ($estudio) use ($anyo, $dir_paim) {
                $pp = PlanPublicado::find()->where(
                    [
                        'estudio_id' => $estudio->id,
                        'anyo' => $anyo,
                        'version' => PlanPublicado::MAX_VERSION_PLAN,
                    ]
                )->one();

                if ($pp != null) {
                    $fichero_paim = "plan-es-{$estudio->id_nk}-v{$pp->getVersionMaxima()}.pdf";
                    return file_exists("{$dir_paim}/{$fichero_paim}");
                }

                return false;
            }
        );

        if (empty($estudios)) {
            throw new ServerErrorHttpException(
                sprintf(Yii::t('cati', 'No hay publicado ningún plan de mejora del curso %d/%d.'), $anyo, $anyo + 1)
            );
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');

        return $this->renderPartial(
            'marc-xmls',
            [
                'anyo' => intval($anyo),
                'estudios' => $estudios,
            ]
        );
    }

    /**
     * Importa la acción indicada de un PAIM anterior.
     */
    public function actionImportarAccion()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $accion_id = $request->post('accion_id');
            $estudio_id = $request->post('estudio_id');
            $apartado_id = $request->post('apartado_id');
            $plan_pregunta_id = $request->post('plan_pregunta_id');
        }

        if (!$accion_id) {
            throw new HttpException(404, Yii::t('cati', 'No ha introducido ningún ID de acción.  ☹'));
        }

        $accion = PlanRespuesta::findOne(['id' => $accion_id]);
        if (!$accion) {
            throw new HttpException(404, Yii::t('cati', 'No se ha encontrado esa acción.  ☹'));
        }
        $estudio = Estudio::getEstudio($estudio_id);

        if ($accion->estudio_id_nk != $estudio->id_nk) {
            throw new ServerErrorHttpException(
                Yii::t(
                    'cati',
                    'El estudio de la acción no coincide con el estudio actual.'
                )
            );
        }

        if ($estudio->anyo_academico != $accion->anyo + 1) {
            throw new ServerErrorHttpException(
                Yii::t(
                    'cati',
                    'Esta acción no es del PAIM anterior.'
                )
            );
        }

        $nuevaRespuesta = new PlanRespuesta();
        $nuevaRespuesta->estudio_id = $estudio_id;
        $nuevaRespuesta->anyo = $estudio->anyo_academico;
        $nuevaRespuesta->plan_pregunta_id = $plan_pregunta_id;
        $nuevaRespuesta->apartado = $apartado_id;
        $nuevaRespuesta->estudio_id_nk = $estudio->id_nk;
        $nuevaRespuesta->cumplimiento = $accion->cumplimiento;  # Sin uso
        // En la tabla `paim_opcion` para cada año hay diferentes registros de
        // ambito_id, responsable_aprobacion_id, plazo_id, apartado_memoria_id, tipo_modificacion_id y estado_id.
        # $nuevaRespuesta->ambito_id= $accion->ambito_id;
        # $nuevaRespuesta->responsable_aprobacion_id = $accion->responsable_aprobacion_id;
        # $nuevaRespuesta->plazo_id = $accion->plazo_id;
        # $nuevaRespuesta->apartado_memoria_id = $accion->apartado_memoria_id;
        # $nuevaRespuesta->tipo_modificacion_id = $accion->tipo_modificacion_id;
        $nuevaRespuesta->seguimiento_id = $accion->seguimiento_id; # Sin uso
        # $nuevaRespuesta->estado_id = $accion->estado_id;

        $nuevaRespuesta->language = $accion->language;
        $nuevaRespuesta->apartado_memoria = $accion->apartado_memoria;
        $nuevaRespuesta->titulo = $accion->titulo;
        $nuevaRespuesta->descripcion_breve = $accion->descripcion_breve;
        $nuevaRespuesta->descripcion_amplia = $accion->descripcion_amplia;
        $nuevaRespuesta->responsable_accion = $accion->responsable_accion;
        $nuevaRespuesta->inicio = $accion->inicio;
        $nuevaRespuesta->final = $accion->final;
        $nuevaRespuesta->responsable_competente = $accion->responsable_competente;
        $nuevaRespuesta->justificacion = $accion->justificacion;
        $nuevaRespuesta->nivel = $accion->nivel;
        $nuevaRespuesta->fecha = $accion->fecha;
        $nuevaRespuesta->problema = $accion->problema;
        $nuevaRespuesta->objetivo = $accion->objetivo;
        $nuevaRespuesta->acciones = $accion->acciones;
        $nuevaRespuesta->plazo_implantacion = $accion->plazo_implantacion;  # Sin uso
        $nuevaRespuesta->indicador = $accion->indicador;
        $nuevaRespuesta->valores_a_alcanzar = $accion->valores_a_alcanzar;
        $nuevaRespuesta->valores_alcanzados = $accion->valores_alcanzados;
        $nuevaRespuesta->necesidad_detectada= $accion->necesidad_detectada;
        $nuevaRespuesta->observaciones = $accion->observaciones;
        $nuevaRespuesta->justificacion_breve = $accion->justificacion_breve;
        try {
            $nuevaRespuesta->save();
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            Yii::$app->getSession()->addFlash('error', $msg);
            // $nuevaRespuesta->addError('_exception', $msg);
        }

        return $this->redirect(
            [
                'ver',
                'estudio_id' => $nuevaRespuesta->estudio_id,
                'anyo' => $estudio->anyo_academico,
            ]
        );
    }

    /**
     * Genera un MarcXML para exportar los planes de mejora de Doctorado de un año.
     *
     * Puede generar el MarcXML de un Programa de Doctorado dado o de todos.
     */
    public function actionMarcXmlDoct($anyo, $estudio_id = null)
    {
        Yii::$app->language = 'es';
        $dir_paim = Yii::getAlias('@webroot') . "/pdf/planes-mejora/{$anyo}";
        if ($estudio_id) {
            $estudios = [Estudio::getEstudio($estudio_id)];
        } else {
            $estudios = Estudio::find()
                ->where(['anyo_academico' => $anyo])
                ->andWhere(['tipoEstudio_id' => Estudio::DOCT_TIPO_ESTUDIO_ID])
                ->all();
        }

        $max_version_plan_doct = PlanPublicado::MAX_VERSION_PLAN_DOCT;
        # En el curso 2021-22, PlanPublicado::MAX_VERSION_PLAN_DOCT pasó de ser 1 a 2.
        if ($anyo < 2021) {
            $max_version_plan_doct -= 1;
        }

        $estudios = array_filter(
            $estudios,
            function ($estudio) use ($anyo, $dir_paim, $max_version_plan_doct) {
                $pp = PlanPublicado::find()->where(
                    [
                        'estudio_id' => $estudio->id,
                        'anyo' => $anyo,
                        'version' => $max_version_plan_doct,
                    ]
                )->one();

                if ($pp != null) {
                    $fichero_paim = "plan-es-{$estudio->id_nk}-v{$pp->getVersionMaxima()}.pdf";
                    return file_exists("{$dir_paim}/{$fichero_paim}");
                }

                return false;
            }
        );

        if (empty($estudios)) {
            throw new ServerErrorHttpException(
                sprintf(Yii::t('cati', 'No hay publicado ningún plan de mejora del curso %d/%d.'), $anyo, $anyo + 1)
            );
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');

        return $this->renderPartial(
            'doct/marc-xmls',
            [
                'anyo' => intval($anyo),
                'estudios' => $estudios,
                'max_version_plan_doct' => $max_version_plan_doct,
            ]
        );
    }

    /**
     * Lanzar la carga de los planes de mejora en Zaguán
     */
    public function actionCargarAZaguan($anyo, $tipo)
    {
        if ($tipo == 'grado-master') {
            $fichero = @fopen(Url::to(['plan-mejora/marc-xml', 'anyo' => $anyo], true), 'rb');
        } elseif ($tipo == 'doctorado') {
            $fichero = @fopen(Url::to(['plan-mejora/marc-xml-doct', 'anyo' => $anyo], true), 'rb');
        } else {
            throw new ServerErrorHttpException(Yii::t('cati', 'Tipo de estudio desconocido.'));
        }
        if (!$fichero) {
            throw new ServerErrorHttpException(sprintf(Yii::t('cati', 'No hay publicado ningún plan de mejora del curso %d/%d.'), $anyo, $anyo + 1));
        }
        $contenido = stream_get_contents($fichero);
        fclose($fichero);

        $temp = tmpfile();
        fwrite($temp, $contenido);
        $meta_data = stream_get_meta_data($temp);
        $ruta = $meta_data['uri'];
        $cfile = new \CURLFile($ruta, 'application/xml');

        $wsUrl = 'https://zaguan.unizar.es/batchuploader/robotupload';
        // Crear el recurso de cURL
        $curlHandle = curl_init();
        // Establecer las opciones
        curl_setopt_array(
            $curlHandle,
            [
                // TRUE para devolver el resultado de la transferencia como una cadena
                // del valor devuelto por curl_exec() en lugar de mostrarlo directamente.
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FAILONERROR => true,
                CURLOPT_URL => $wsUrl,
                // CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'zaguan_estudios',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => [
                    'file' => $cfile,
                    'mode' => '-ir',
                ],
            ]
        );
        // Enviar la petición y guardar la respuesta en la variable
        $respuesta = curl_exec($curlHandle);
        if (false === $respuesta) {
            throw new ServerErrorHttpException(
                'Error: "' . curl_error($curlHandle)
                . '" - Cod: ' . curl_errno($curlHandle)
            );
        }
        // Cerrar el recurso de curl para liberar recursos del sistema
        curl_close($curlHandle);
        fclose($temp);

        $nombre_usuario = Yii::$app->user->identity->username;
        $texto = "{$nombre_usuario} ha lanzado la carga a Zaguan de los planes de innovación y mejora.";
        $mensaje = Yii::$app->mailer->compose()
            ->setFrom([Yii::$app->params['adminEmail'] => 'Robot Estudios'])
            ->setTo(Yii::$app->params['adminZaguan'])
            ->setSubject('Lanzada carga a Zaguan')
            ->setTextBody($texto);
        // ->setHtmlBody('<b>HTML content</b>');

        $mensaje->send();

        Yii::info($texto, 'gestion');

        return $this->render(
            '//gestion/zaguan-resultado',
            [
                'anyo' => $anyo,
                'respuesta' => $respuesta,
                'tipo' => $tipo,
            ]
        );
    }
}
