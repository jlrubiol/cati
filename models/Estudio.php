<?php
/**
 * Modelo de la tabla Estudio.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */

namespace app\models;

use app\models\base\Estudio as BaseEstudio;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use app\models\Calendario;

/**
 * This is the model class for table "estudio".
 *
 * El nombre del estudio está internacionalizado (tabla `estudio_lang`).
 * Las traducciones proceden de la tabla de Sigma `VEGA.DGES_DESCRIPCIONES` por medio del ODS (grado y máster)
 * o de la pasarela `descripciones_doctorado.ktr` (programas de doctorado).
 */
class Estudio extends BaseEstudio
{
    // En la BD se ha creado un estudio ficticio para elaborar el Informe de la
    // Calidad de los Estudios de Doctorado y de sus diferentes programas.
    const ICED_ESTUDIO_ID = 99999;
    const GRADO_TIPO_ESTUDIO_ID = 5;
    const MASTER_TIPO_ESTUDIO_ID = 6;
    const DOCT_TIPO_ESTUDIO_ID = 7;
    const ICED_TIPO_ESTUDIO_ID = 99;
    // Las dobles titulaciones no existen como tal.
    // No se hacen informes, ni memorias, ni renovación de la acreditación.
    // 154: Programa conjunto en ADE/Derecho
    // 159: Programa conjunto en Física-Matemáticas
    // 160: Programa conjunto en Nutrición Humana y Dietética-Ciencias de la Actividad Física y del Deporte
    // 161: Programa conjunto en Matemáticas-Ingeniería Informática
    // 162: Programa conjunto en Ingeniería Mecatrónica-Ingeniería de Organización Industrial
    // 165: Programa conjunto en Ingeniería Informática-Administración y Dirección de Empresas
    // 709: Programa conjunto en Máster Universitario en Ingeniería Industrial-Máster Universitario en Ingeniería Mecánica
    // 710: Programa conjunto en Máster Universitario en Ingeniería Industrial-Máster Universitario en Energías Renovables y Eficiencia Energética
    // 711: Programa conjunto en Máster Universitario en Ingeniería Industrial-Máster Universitario en Ingeniería Electrónica

    const FALSOS_ESTUDIO_IDS = [154, 159, 160, 161, 162, 165, 709, 710, 711];
    const CENTROS_PROGRAMAS_CONJUNTOS = [
        // El Programa conjunto en ADE/Derecho (154) tiene un único plan (432), que se imparte en 2 centros:
        // la Facultad de Derecho (102) y la Facultad de Economía y Empresa (159).
        154 => [102, 109],
        159 => [100,],
        160 => [229,],
        // El Programa conjunto en Matemáticas-Ingeniería Informática (161) tiene un único plan (607),
        // que se imparte en 2 centros: la Facultad de Ciencias (100) y la EINA (110).
        161 => [100, 110],
        162 => [175,],
        165 => [301, 326],
        709 => [110],
        710 => [110],
        711 => [110],
    ];

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
            'codigo_mec' => Yii::t('models', 'Código MEC'),
            'rama_id' => Yii::t('models', 'ID de la rama'),
            'tipoEstudio_id' => Yii::t('models', 'ID del tipo de estudio'),
            'nombre_coordinador' => Yii::t('models', 'Nombre del coordinador'),
            'email_coordinador' => Yii::t('models', 'Email del coordinador'),
            'anyo_academico' => Yii::t('models', 'Año académico'),
            'id_nk' => Yii::t('models', 'Cód. estudio'),
            'fecha_implantacion' => Yii::t('models', 'Fecha de implantación'),
            'fecha_acreditacion' => Yii::t('models', 'Fecha de última renovación'),
            'anyos_evaluacion' => Yii::t('models', 'Años del periodo de evaluación'),
        ];
    }

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                // custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                // custom validation rules
            ]
        );
    }

    /**
     * Si el estudio se imparte en algún centro acreditado, lo devuelve. Si no, False.
     */
    public function getCentroAcreditado()
    {
        $centros = $this->getCentros();
        // Se toma como centro el que haya sido acreditado más recientemente.
        usort(
            $centros,
            function ($centro1, $centro2) {
                return $centro1->fecha_acreditacion < $centro2->fecha_acreditacion;
            }
        );
        foreach ($centros as $centro) {
            if ($centro->fecha_acreditacion) {
                return $centro;
            }
        }
        return false;
    }

    /**
     * Devuelve la URL donde se encuentra la memoria de verificación del estudio.
     */
    public function getEnlaceMemoriaVerificacion()
    {
        $base = "https://academico.unizar.es/sites/academico/files/archivos/ofiplan/memorias";
        $carpeta = "grado";
        if ($this->tipoEstudio_id == Estudio::MASTER_TIPO_ESTUDIO_ID) {
            $carpeta = "master";
        } elseif ($this->tipoEstudio_id == Estudio::DOCT_TIPO_ESTUDIO_ID) {
            $carpeta = "Doctorado";
        }
        $ramas = [
            'H' => "artes",
            "X" => "ciencias",
            'J' => "sociales",
            "S" => "salud",
            "T" => "ingenieria",
        ];
        $subcarpeta = $ramas[$this->rama_id];
        $enlace = "{$base}/{$carpeta}/{$subcarpeta}/mv_{$this->id_nk}.pdf";
        return $enlace;
    }

    /**
     * Devuelve el nombre de la titulación en formato adecuado para URLs.
     *
     * @return
     *   A string representing the slug
     */
    public function getSlug()
    {
        // Convert to lowercase
        $slug = mb_strtolower($this->nombre);

        // Replace letters with accents by their non-accented counterparts.
        // I do not consider ð, þ, š nor non latin alphabets, which should be transliterated.
        $accents = '/&([a-z]{1,2})(grave|acute|circ|tilde|uml|ring|lig|cedil|slash);/';
        $slug = preg_replace($accents, '$1', htmlentities($slug));

        // Replace spaces by dashes
        $slug = strtr($slug, [' ' => '-']);

        // Remove non alphanumeric or dash characters
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);

        // Replace multiple dashes by a single one
        $slug = preg_replace('/[\-]+/', '-', $slug);

        return $slug;
    }

    public function getInforme($anyo)
    {
        $language = Yii::$app->language;
        $informe = InformePublicado::find()
            ->where(['estudio_id' => $this->id, 'anyo' => $anyo, 'language' => $language])
            ->one();

        return $informe;
    }

    /**
     * Devuelve la versión del informe publicado del estudio en el año indicado.
     */
    public function getVersionInforme($anyo)
    {
        $informe = self::getInforme($anyo);

        return $informe ? $informe->version : 0;
    }

    /**
     * Devuelve el listado de los planes de mejora de un año y tipo de estudio.
     */
    public function getListadoPlanes($anyo, $language, $tipo)
    {
        /*
            SELECT e.id,
                   e.id_nk,
                   el.nombre,
                   CASE
                       WHEN pp.version IS NULL THEN 0
                       ELSE pp.version
                   END AS version,
                   FLOOR(COUNT(pr.estudio_id)/COUNT(DISTINCT p.id)) AS celdas,
                   -- XXX GROUP_CONCAT is not standard SQL.
                   -- On PostgreSQL use string_agg, or array_to_string + array_agg.
                   -- On Oracle use LISTAGG
                   GROUP_CONCAT(DISTINCT p.email_coordinador SEPARATOR ', ') AS coordinadores
              FROM estudio e
        INNER JOIN estudio_lang el
                ON e.id = el.estudio_id AND el.language = 'es'
         LEFT JOIN plan_publicado pp
                ON e.id = pp.estudio_id AND pp.language = 'es' -- AND pp.anyo = 2015
         LEFT JOIN plan_respuesta pr
                ON e.id = pr.estudio_id AND pr.anyo = 2015
         LEFT JOIN plan_respuesta_lang prl
                ON prl.plan_respuesta_id = pr.id AND prl.language = 'es'
         LEFT JOIN plan p
                ON e.id = p.estudio_id AND p.activo = 1
             WHERE e.anyo = 2015 AND e.tipoEstudio_id IN (5, 6) AND e.activo = 1
          GROUP BY e.id, el.nombre, pp.version
          ORDER BY el.nombre
        ;
        */
        if ('grado-master' == $tipo) {
            $condicion = sprintf(
                'e.tipoEstudio_id IN (%d, %d) AND e.id_nk NOT IN (%s)',
                self::GRADO_TIPO_ESTUDIO_ID,
                self::MASTER_TIPO_ESTUDIO_ID,
                implode(', ', self::FALSOS_ESTUDIO_IDS)
            );
        } elseif ('doctorado' == $tipo) {
            $condicion = sprintf('e.tipoEstudio_id = %d', self::DOCT_TIPO_ESTUDIO_ID);
        } else {
            throw new NotFoundHttpException(
                sprintf(
                    Yii::t('cati', 'No se ha encontrado el tipo de estudio «%s».  ☹'),
                    $tipo
                )
            );
        }

        $max_version_plan_doct = PlanPublicado::MAX_VERSION_PLAN_DOCT;
        # En el curso 2021-22, PlanPublicado::MAX_VERSION_PLAN_DOCT pasó de ser 1 a 2.
        if ($anyo < 2021) {
            $max_version_plan_doct -= 1;
        }

        $datos = (new Query())
            ->select(
                [
                    'e.id',
                    'e.id_nk',
                    'el.nombre',
                    'version' => 'CASE
                        WHEN [[pp.version]] IS NULL THEN 0
                        ELSE [[pp.version]]
                    END',
                    'version_maxima' => 'CASE
                        WHEN [[e.tipoEstudio_id]] IN (5, 6) THEN ' . PlanPublicado::MAX_VERSION_PLAN . '
                        WHEN [[e.tipoEstudio_id]] = 7 THEN ' . $max_version_plan_doct . '
                    END',
                    'celdas' => 'FLOOR(COUNT(pr.estudio_id)/COUNT(DISTINCT p.id))',
                    'coordinadores' => "GROUP_CONCAT(DISTINCT p.email_coordinador SEPARATOR ', ')", /* XXX */
                ]
            )
            ->from(['e' => 'estudio'])
            ->join(
                'INNER JOIN',
                ['el' => 'estudio_lang'],
                'e.id = el.estudio_id AND el.language = :language',
                [':language' => $language]
            )
            ->join(
                'LEFT JOIN',
                ['pp' => 'plan_publicado'],
                'e.id = pp.estudio_id AND pp.language = :language',
                [':language' => $language]
            )
            ->join(
                'LEFT JOIN',
                ['pr' => 'plan_respuesta'],
                'e.id = pr.estudio_id AND pr.anyo = :anyo',
                [':anyo' => $anyo]
            )
            ->join(
                'LEFT JOIN',
                ['prl' => 'plan_respuesta_lang'],
                'prl.plan_respuesta_id = pr.id AND prl.language = :language',
                [':language' => $language]
            )
            ->join('LEFT JOIN', ['p' => 'plan'], 'e.id = p.estudio_id AND p.activo = 1')
            ->where(['e.anyo_academico' => $anyo])
            ->andWhere($condicion)  // Diferenciar Grado-Máster y Doctorado
            ->andWhere(['e.activo' => 1])
            ->groupBy('e.id, e.id_nk, e.tipoEstudio_id, el.nombre, pp.version')
            ->all();

        return $datos;
    }

    /**
     * Devuelve un array con las direcciones de correo de los coordinadores de los planes de un estudio.
     */
    public function getCoordinadores()
    {
        if ($this->esDoctorado()) {
            return $this->getCoordinadoresDoctorado();
        }

        $coordinadores = Agente::find()
            ->select(['email' => 'LOWER(email)'])
            ->where(['estudio_id_nk' => $this->id_nk])
            ->andWhere(['comision_id' => 'O'])
            ->asArray()
            ->all();
        $coordinadores = ArrayHelper::getColumn($coordinadores, 'email');
        $coordinadores_validos = array_filter(
            $coordinadores, function ($direccion) {
                return filter_var($direccion, FILTER_VALIDATE_EMAIL);
            }
        );

        return $coordinadores_validos;
    }

    /**
     * Devuelve las direcciones de los delegados de los planes de un estudio.
     */
    public function getDelegados()
    {
        $delegados = Agente::find()
            ->select(['email' => 'LOWER(email)'])
            ->where(['estudio_id_nk' => $this->id_nk])
            ->andWhere(['comision_id' => 'delegado'])
            ->asArray()
            ->all();
        $delegados = ArrayHelper::getColumn($delegados, 'email');
        $delegados_validos = array_filter(
            $delegados, function ($direccion) {
                return filter_var($direccion, FILTER_VALIDATE_EMAIL);
            }
        );

        return $delegados_validos;
    }

    /**
     * Devuelve las direcciones de los coordinadores y delegados de los planes de un estudio.
     */
    public function getCoordinadoresYDelegados()
    {
        $coorDeles = Agente::find()
            ->select(['email' => 'LOWER(email)'])
            ->where(['estudio_id_nk' => $this->id_nk])
            ->andWhere(['comision_id' => ['O', 'delegado']])
            ->asArray()
            ->all();
        $coorDeles = ArrayHelper::getColumn($coorDeles, 'email');
        $coorDeleValidos = array_filter(
            $coorDeles, function ($direccion) {
                return filter_var($direccion, FILTER_VALIDATE_EMAIL);
            }
        );

        return $coorDeleValidos;
    }

    /**
     * Devuelve las direcciones de los coordinadores y delegados de los planes de un estudio.
     */
    public function getNipCoordinadoresYDelegados()
    {
        $coorDeles = Agente::find()
            ->select('nip')
            ->where(['estudio_id_nk' => $this->id_nk])
            ->andWhere(['comision_id' => ['O', 'delegado']])
            ->column();

        return $coorDeles;
    }

    /**
     * Devuelve las direcciones de los coordinadores de un programa de doctorado.
     */
    public function getCoordinadoresDoctorado()
    {
        $coordinadores = self::find()
            ->select(['email' => 'LOWER(plan.email_coordinador)'])
            ->innerJoin('plan', 'estudio.id = plan.estudio_id')
            ->where(['estudio_id' => $this->id])
            ->andWhere(['plan.activo' => 1])
            ->asArray()
            ->all();
        $coordinadores = ArrayHelper::getColumn($coordinadores, 'email');
        $coordinadores_validos = array_filter(
            $coordinadores, function ($direccion) {
                return filter_var($direccion, FILTER_VALIDATE_EMAIL);
            }
        );

        return $coordinadores_validos;
    }

    /**
     * Devuelve las direcciones de los coordinadores de todos los programas de doctorado.
     */
    public function getTodosCoordinadoresDoctorado()
    {
        $coordinadores = self::find()
            ->select(['email' => 'LOWER(plan.email_coordinador)'])
            ->innerJoin('plan', 'estudio.id = plan.estudio_id')
            ->where(['tipoEstudio_id' => self::DOCT_TIPO_ESTUDIO_ID])
            ->andWhere(['estudio.activo' => 1])
            ->andWhere(['plan.activo' => 1])
            ->asArray()
            ->all();
        $coordinadores = ArrayHelper::getColumn($coordinadores, 'email');
        $coordinadores_validos = array_filter(
            $coordinadores, function ($direccion) {
                return filter_var($direccion, FILTER_VALIDATE_EMAIL);
            }
        );

        return $coordinadores_validos;
    }

    /**
     * Devuelve las direcciones de los presidentes de las comisiones de garantía.
     */
    public function getPresidentesGarantia()
    {
        $presidentes = Agente::find()
            ->select(['email' => 'LOWER(email)'])
            ->where(['estudio_id_nk' => $this->id_nk])
            ->andWhere(['comision_id' => 'G'])
            ->andWhere(['rol' => ['Presidente', 'Presidenta']])
            ->asArray()
            ->all();
        $presidentes = ArrayHelper::getColumn($presidentes, 'email');
        $presidentes_validos = array_filter(
            $presidentes, function ($direccion) {
                return filter_var($direccion, FILTER_VALIDATE_EMAIL);
            }
        );

        return $presidentes_validos;
    }

    /**
     * Devuelve las direcciones de los presidentes de las comisiones de garantía y sus delegados,
     * y presidentes de las Comisiones de Garantía de la Calidad Conjunta.
     */
    public function getPresidentesGarantiaYDelegados()
    {
        $presidentes = Agente::find()
            ->select(['email' => 'LOWER(email)'])
            ->where(['estudio_id_nk' => $this->id_nk])
            ->andWhere(['comision_id' => ['G', 'dele_cgc', 'C']])
            ->andWhere(['rol' => ['Presidente', 'Presidenta', 'Delegado presidente CGC']])
            ->asArray()
            ->all();
        $presidentes = ArrayHelper::getColumn($presidentes, 'email');
        $presidentes_validos = array_filter(
            $presidentes, function ($direccion) {
                return filter_var($direccion, FILTER_VALIDATE_EMAIL);
            }
        );

        return $presidentes_validos;
    }

    /**
     * Devuelve los NIP de los presidentes de las comisiones de garantía.
     */
    public function getNipPresidentesGarantia()
    {
        $presidentes = Agente::find()
            ->select('nip')
            ->where(['estudio_id_nk' => $this->id_nk])
            ->andWhere(['comision_id' => 'G'])
            ->andWhere(['rol' => ['Presidente', 'Presidenta']])
            ->column();

        return $presidentes;
    }

    /**
     * Devuelve los NIP de los presidentes de las comisiones de garantía y sus delegados.
     */
    public function getNipPresidentesGarantiaYDelegados()
    {
        $presidentes = Agente::find()
            ->select('nip')
            ->where(['estudio_id_nk' => $this->id_nk])
            ->andWhere(['comision_id' => ['G', 'dele_cgc']])
            ->andWhere(['rol' => ['Presidente', 'Presidenta', 'Delegado presidente CGC']])
            ->column();

        return $presidentes;
    }

    /**
     * Devuelve las direcciones de los expertos del rector.
     */
    public function getExpertosRector()
    {
        $expertos = Agente::find()
            ->select(['email' => 'LOWER(email)'])
            ->where(['estudio_id_nk' => $this->id_nk])
            ->andWhere(['comision_id' => 'E'])
            ->andWhere(['rol' => ['Experto externo del rector', 'Experta externa del rector']])
            ->asArray()
            ->all();
        $expertos = ArrayHelper::getColumn($expertos, 'email');
        $expertos_validos = array_filter(
            $expertos, function ($direccion) {
                return filter_var($direccion, FILTER_VALIDATE_EMAIL);
            }
        );

        return $expertos_validos;
    }

    /**
     * Devuelve los emails de los decanos de los centros en los que se imparte algún plan de este estudio.
     */
    public function getDecanos()
    {
        $planes = $this->getPlans()->where(['activo' => 1])->all();
        $decanos = array_map(
            function ($p) {
                return strtolower($p->centro->email_decano);
            }, $planes
        );
        $decanos_validos = array_filter(
            $decanos, function ($direccion) {
                return filter_var($direccion, FILTER_VALIDATE_EMAIL);
            }
        );

        return $decanos_validos;
    }

    public function getCentros()
    {
        $planes = $this->getPlans()->where(['activo' => 1])->orderBy('centro_id')->all();
        $centros = array_unique(
            array_map(
                function ($p) {
                    return $p->centro;
                }, $planes
            )
        );

        if (in_array($this->id_nk, Estudio::FALSOS_ESTUDIO_IDS)) {
            $centros = Centro::findAll(['id' => Estudio::CENTROS_PROGRAMAS_CONJUNTOS[$this->id_nk]]);
        }

        /*
        usort(
            $centros, function ($centro1, $centro2) {
                return $centro1->nombre > $centro2->nombre;
            }
        );
        */

        return $centros;
    }

    /**
     * Devuelve los planes del estudio que se imparten en cada centro.
     */
    public function getPlanesPorCentro()
    {
        $planes = $this->getPlans()->orderBy(['centro_id' => SORT_ASC, 'en_extincion' => SORT_ASC, 'id_nk' => SORT_ASC])->all();

        $planes_por_centro = [];
        foreach ($planes as $plan) {
            if ($plan->activo) {
                $planes_por_centro[$plan->centro->id][] = $plan;
            }
        }

        if (in_array($this->id_nk, Estudio::FALSOS_ESTUDIO_IDS)) {
            $planes_por_centro = [];
            $ids_centros = Estudio::CENTROS_PROGRAMAS_CONJUNTOS[$this->id_nk];
            foreach($ids_centros as $id_centro) {
                $planes_por_centro[$id_centro][] = $planes[0];
            }
        }

        return $planes_por_centro;
    }

    /**
     * Devuelve un array con los id's y nombres de los estudios activos en el año académico indicado.
     * Se emplea en el buscador de la página de inicio.
     */
    public static function getEstudiosActivos($anyo_academico = null)
    {
        $language = Yii::$app->language;
        if (!$anyo_academico) {
            $anyo_academico = Calendario::getAnyoAcademico();
        }
        $activos = self::find()
            ->where(
                [
                    'in',
                    'tipoEstudio_id',
                    [self::GRADO_TIPO_ESTUDIO_ID, self::MASTER_TIPO_ESTUDIO_ID, self::DOCT_TIPO_ESTUDIO_ID],
                ]
            )
            ->andWhere(['estudio.anyo_academico' => $anyo_academico, 'language' => $language])
            ->innerJoinWith('translations')
            ->select(
                [
                    'id_nk as value',
                    'nombre as label',
                    'id_nk as id',
                ]
            )->orderBy('label')->asArray()->all();

        return $activos;
    }

    public function hayBorrador()
    {
        $num_respuestas = InformeRespuesta::find()
            ->where(['estudio_id' => $this->id])
            ->count();
        $hay_borrador = $num_respuestas > 0;

        return $hay_borrador;
    }

    public function hayBorradorIced($anyo)
    {
        $num_respuestas = InformeRespuesta::find()
            ->where(['estudio_id_nk' => Estudio::ICED_ESTUDIO_ID, 'anyo' => $anyo])
            ->count();
        $hay_borrador = $num_respuestas > 0;

        return $hay_borrador;
    }

    /**
     * Devuelve si el estudio es de tipo Grado.
     */
    public function esGrado()
    {
        return self::GRADO_TIPO_ESTUDIO_ID === $this->tipoEstudio_id;
    }

    /**
     * Devuelve si el estudio es de tipo Máster.
     */
    public function esMaster()
    {
        return self::MASTER_TIPO_ESTUDIO_ID === $this->tipoEstudio_id;
    }

    /**
     * Devuelve si el estudio es de tipo Grado o Máster.
     */
    public function esGradoOMaster()
    {
        return in_array($this->tipoEstudio_id, [self::GRADO_TIPO_ESTUDIO_ID, self::MASTER_TIPO_ESTUDIO_ID]);
    }

    /**
     * Devuelve si el estudio es de tipo Doctorado.
     */
    public function esDoctorado()
    {
        return self::DOCT_TIPO_ESTUDIO_ID === $this->tipoEstudio_id;
    }

    /**
     * Devuelve si es el estudio ficticio creado para elaborar el ICED.
     */
    public function esIced()
    {
        return self::ICED_TIPO_ESTUDIO_ID === $this->tipoEstudio_id;
    }

    /**
     * Devuelve el método empleado para visualizar el estudio.
     */
    public function getMetodoVerEstudio()
    {
        if ($this->esGradoOMaster()) {
            return 'estudio/ver';
        } elseif ($this->esDoctorado()) {
            return 'estudio/ver-doct';
        } else {
            return 'FIXME';
        }

        throw new NotFoundHttpException(
            sprintf(
                Yii::t('cati', 'Este tipo de estudio no tiene informes.  ☹')
            )
        );
    }

    /**
     * Devuelve el método empleado para visualizar el informe.
     */
    public function getMetodoVerInforme()
    {
        if ($this->esGradoOMaster()) {
            return 'informe/ver';
        } elseif ($this->esDoctorado()) {
            return 'informe/ver-doct';
        } elseif ($this->esIced()) {
            return 'informe/ver-iced';
        } else {
            return 'FIXME';
        }

        throw new NotFoundHttpException(
            sprintf(
                Yii::t('cati', 'Este tipo de estudio no tiene informes.  ☹')
            )
        );
    }

    /**
     * Devuelve el método empleado para editar el informe.
     */
    public function getMetodoEditarInforme()
    {
        if ($this->esGradoOMaster()) {
            return 'informe/editar';
        } elseif ($this->esDoctorado()) {
            return 'informe/editar-doct';
        } elseif ($this->esIced()) {
            return 'informe/editar-iced';
        } else {
            return 'FIXME';
        }

        throw new NotFoundHttpException(
            sprintf(
                Yii::t('cati', 'Este tipo de estudio no tiene informes.  ☹')
            )
        );
    }

    /**
     * Devuelve la descripción del tipo de estudio.
     */
    public function getTipoEstudio()
    {
        if ($this->esGradoOMaster()) {
            return 'grado-master';
        } elseif ($this->esDoctorado()) {
            return 'doctorado';
        } elseif ($this->esIced()) {
            return 'iced';
        } else {
            return 'FIXME';
        }

        throw new NotFoundHttpException(
            sprintf(
                Yii::t('cati', 'Este tipo de estudio no tiene descripción.  ☹')
            )
        );
    }

    /**
     * Finds the Estudio model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @throws HttpException if the model cannot be found
     *
     * @param int $id
     *
     * @return Estudio the loaded model
     */
    public static function getEstudio($id)
    {
        if (null !== ($model = self::findOne(['id' => $id]))) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese estudio.  ☹'));
    }

    /**
     * Busca un estudio usando su ID nativo y año académico.
     */
    public static function getEstudioByNk($anyo_academico, $estudio_id_nk)
    {
        if (null !== ($model = self::find()->where(['anyo_academico' => $anyo_academico, 'id_nk' => $estudio_id_nk])->one())) {
            return $model;
        }
        Yii::debug("Estudio no encontrado: Año {$anyo_academico} NK {$estudio_id_nk}");
        throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese estudio y año.  ☹'));
    }

    /**
     * Busca un estudio usando su ID nativo.
     *
     * Devuelve el del último año disponible.
     */
    public static function getUltimoEstudioByNk($estudio_id_nk)
    {
        if (($model = self::find()->where(['id_nk' => $estudio_id_nk])->orderBy(['anyo_academico' => SORT_DESC])->one()) == null) {
            throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese estudio.  ☹'));
        }
        /*
        if ($model->esDoctorado()) {
            $anyo = Calendario::getAnyoDoctorado() + 1;
            $model = self::find()->where(['id_nk' => $estudio_id_nk, 'anyo_academico' => $anyo])->one();
        }
        */
        return $model;
    }

    /**
     * Devuelve todos los estudios de una rama y año clasificados por tipo de estudio.
     */
    public static function getEstudiosDeLaRama($anyo_academico, $rama_id)
    {
        $query = self::find()
            ->where(['estudio.anyo_academico' => $anyo_academico])
            ->andWhere(['rama_id' => $rama_id])
            ->andWhere(['language' => Yii::$app->language])
            ->andWhere(['activo' => 1])
            ->innerJoinWith('translations')
            ->orderBy('tipoEstudio_id')
            ->addOrderBy('nombre');
        $estudios = $query->all();

        $tipos = TipoEstudio::find()->innerJoinWith('translations')->where(['language' => Yii::$app->language])->all();
        $estudios2 = [];
        foreach ($tipos as $tipo) {
            $estudios2[$tipo->nombre] = array_filter(
                $estudios, function ($estudio) use ($tipo) {
                    return $estudio->tipoEstudio_id === $tipo->id;
                }
            );
        }
        // Quitamos los tipos que no tienen estudios en esta rama
        $estudios3 = array_filter($estudios2, 'count');

        return $estudios3;
    }

    /**
     * Devuelve todos los estudios del tipo y año indicados, por orden alfabético.
     *
     * @param int $tipo
     *
     * @return Estudio[] todos los estudios activos de este tipo
     */
    public static function getEstudiosDelTipo($anyo_academico, $tipo)
    {
        $query = self::find()
            ->where(['estudio.anyo_academico' => $anyo_academico])
            ->andWhere(['tipoEstudio_id' => $tipo])
            ->andWhere(['language' => Yii::$app->language])
            ->andWhere(['activo' => 1])
            ->innerJoinWith('translations')
            ->orderBy('nombre');
        // print_r $query->createCommand()->getRawSql(); // DEBUG
        // die();
        $estudios = $query->all();

        return $estudios;
    }

    /**
     * Devuelve las claves naturales de todos los estudios del tipo indicado.
     *
     * @param int $tipo
     *
     * @return int[] todos los id_nk de los estudios de este tipo
     */
    public static function getIdnkEstudiosDelTipo($tipo)
    {
        $query = self::find()
            ->select(['id_nk'])
            ->where(['tipoEstudio_id' => $tipo])
            ->asArray()
            ->distinct();
        // print_r($query->createCommand()->getRawSql());  // DEBUG
        // die();
        $idnkEstudios = $query->column();

        return $idnkEstudios;
    }

    /**
     * Devuelve todos los estudios del tipo y año indicados, clasificados por ramas de conocimiento.
     *
     * @param int $tipo
     *
     * @return Estudio[][] array de macroareas con los estudios activos de este tipo.
     */
    public static function getEstudiosPorRama($anyo_academico, $tipo)
    {
        $query = self::find()
            ->where(['estudio.anyo_academico' => $anyo_academico])
            ->andWhere(['tipoEstudio_id' => $tipo])
            ->andWhere(['language' => Yii::$app->language])
            ->andWhere(['activo' => 1])
            ->innerJoinWith('translations')
            ->orderBy('rama_id')
            ->addOrderBy('nombre');
        $estudios = $query->all();

        // $ramas = array_unique(array_map(function ($estudio) { return $estudio->rama_id; }, $estudios));
        $ramas = Rama::find()
            ->innerJoinWith('translations')
            ->where(['language' => Yii::$app->language])
            ->orderBy('nombre')->all();
        $estudios2 = [];
        foreach ($ramas as $rama) {
            $estudios2[$rama->nombre] = array_filter(
                $estudios, function ($estudio) use ($rama) {
                    return $estudio->rama_id === $rama->id;
                }
            );
        }
        // Quitamos las ramas que no tienen estudios de este tipo
        $estudios3 = array_filter($estudios2, 'count');

        return $estudios3;
    }

    /**
     * Devuelve el nombre de un estudio a partir de un $estudio_id_nk
     *
     * El nombre del estudio es el último disponible.
     */
    public static function getNombreByNk($estudio_id_nk)
    {
        $estudio = self::find()->where(['id_nk' => $estudio_id_nk])->orderBy(['anyo_academico' => SORT_DESC])->one();

        return $estudio ? $estudio->nombre : '';
    }
}
