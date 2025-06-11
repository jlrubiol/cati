<?php

namespace app\models;

use app\models\Calendario;
use app\models\base\Agente as BaseAgente;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "agente".
 */
class Agente extends BaseAgente
{
    /*
       Los coordinadores de grado y máster los introducen desde el Servicio de PDI.
       Los coordinadores de programas de doctorado los introducen desde la Escuela de Doctorado.

       Los miembros de las comisiones de Evaluación de la calidad de la titulación (grado y máster) los introducen los administradores de centro.
       Los miembros de las comisiones de Garantía de la calidad de la titulación los introducen los administradores de centro.
       Los miembros de las comisiones de Evaluación de la calidad del programa de doctorado los introducen en la Escuela de Doctorado.
       Los miembros de las comisiones de Calidad (Plan de calidad) los introducen desde la Inspección General de Servicios.

       Si hay dudas, que escriban a <sps en unizar.es>
     */

    /* Orden en que mostrar las comisiones */
    const LISTA_COMISIONES = [
        'O', // Coordinador del centro
        'E', // Comisión de Evaluación de la Calidad
        'G', // Comisión de Garantía de la Calidad
        'C', // Comisión de Garantía de la Calidad Conjunta
        'A', // Comisión Académica (Doctorado)
        'D', // Comisión de Evaluación de la Calidad del Programa (Doctorado)
             // Comisión de Doctorado
    ];

    /* Orden en que mostrar los miembros de cada comisión */
    const LISTA_ROLES = [
        'Coordinador',
        'Presidenta',
        'Presidente',
        'Secretaria',
        'Secretario',
        'Doctora',
        'Doctor',
        'Miembro',
        'Profesora',
        'Profesor',
        'PAS',
        'Doctorando',
        'Estudiante',
        'Experta externa del rector',
        'Experta externa del centro',
        'Experto externo del rector',
        'Experto externo del centro',
    ];

    /* Nombres de las comisiones */
    const COMISIONES = [
        'A' => 'Comisión Académica',
        'C' => 'Comisión de Garantía de la Calidad Conjunta',
        'D' => 'Comisión de Evaluación de la Calidad del Programa',  # (Doctorado)
        'E' => 'Comisión de Evaluación de la Calidad',
        'G' => 'Comisión de Garantía de la Calidad',
        'O' => 'Coordinador',  // 'Coordinador del centro',
    ];

    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'centro_id' => Yii::t('models', 'ID centro'),
                'plan_id_nk' => Yii::t('models', 'Cód. plan'),
                'estudio_id' => Yii::t('models', 'ID estudio'),
                'comision_id' => Yii::t('models', 'ID comisión'),
                'apellido1' => Yii::t('models', 'Primer apellido'),
                'apellido2' => Yii::t('models', 'Segundo apellido'),
                'email' => Yii::t('models', 'Correo electrónico'),
                'nip' => Yii::t('models', 'NIP'),
                'doc_id' => Yii::t('models', 'Documento identificativo'),
            ]
        );
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
                [['nombre', 'email', 'nip'], 'required'],
                [['email'], 'email'],
            ]
        );
    }

    /**
     * Ordenar el array de agentes por su comisión y rol, según las listas de comisiones y roles.
     */
    private static function cmp($a1, $a2)
    {
        $roles = self::LISTA_ROLES;
        $comisiones = self::LISTA_COMISIONES;

        if ($a1->comision_id != $a2->comision_id) {
            return array_search($a1->comision_id, $comisiones) - array_search($a2->comision_id, $comisiones);
        }

        if ($a1->rol != $a2->rol) {
            return array_search($a1->rol, $roles) - array_search($a2->rol, $roles);
        }

        return $a1->apellido1 > $a2->apellido1;
    }

    /**
     * Devuelve todos los agentes de una titulación clasificados por centro y comisión.
     */
    public static function getAgentesDelEstudio($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $centros = $estudio->getCentros();
        $sin_centro = new Centro(['id' => 0, 'nombre' => '']);  // Las comisiones conjuntas no tienen centro.
        $centros[] = $sin_centro;

        $datos = [];
        foreach ($centros as $centro) {
            foreach (self::LISTA_COMISIONES as $comision_id) {
                $agentes = self::find()
                  ->where([
                        'estudio_id_nk' => $estudio->id_nk,
                        'centro_id' => $centro->id,
                        'comision_id' => $comision_id,
                    ])->all();
                if ($agentes) {
                    usort($agentes, 'self::cmp');
                    $dataProvider = new ArrayDataProvider([
                        'allModels' => $agentes,
                        'pagination' => false,
                        'sort' => [
                            'attributes' => ['rol', 'nombre', 'apellido1', 'apellido2', 'email', 'nip'],
                        ],
                    ]);
                    $datos[$centro->nombre][self::COMISIONES[$comision_id]] = $dataProvider;
                }
            }
        }

        return $datos;
    }

    /**
     * Devuelve los ID nativos de los estudios en los que el nip indicado es coordinador o delegado.
     */
    public static function getIdNkEstudiosCoordinados($nip)
    {
        $query = new Query();
        $query->select('estudio_id_nk')
            ->from('agente')
            ->where([
                'nip' => $nip,
                'comision_id' => ['O', 'delegado'],
            ]);
        $command = $query->createCommand();
        $idNkEstudiosCoordinados = array_map('intval', $command->queryColumn());

        return $idNkEstudiosCoordinados;
    }


    /**
     * Devuelve los ID nativos de los estudios en los que el nip indicado es presidente de la CGC (o delegado).
     */
    public static function getIdNkEstudiosPresididos($nip)
    {
        $query = new Query();
        $query->select('estudio_id_nk')
            ->from('agente')
            ->where([
                'nip' => $nip,
                'comision_id' => ['G', 'dele_cgc'],
                'rol' => ['Presidente', 'Presidenta', 'Delegado presidente CGC'],
            ]);
        $command = $query->createCommand();
        $idNkEstudiosPresididos = array_map('intval', $command->queryColumn());

        return $idNkEstudiosPresididos;
    }

    /**
     * Devuelve los ID nativos de los planes en los que el nip indicado
     * es coordinador, delegado, o presidente de la Comisión de Garantía de la Calidad,
     * o delegado del presidente.
     */
    public static function getIdNkPlanesCoorOPresi($nip)
    {
        $query = new Query();
        $query->select('plan_id_nk')
            ->from('agente')
            ->where([
                'nip' => $nip,
                'comision_id' => ['O', 'delegado'],
            ])
            ->orWhere([
                'nip' => $nip,
                'comision_id' => ['G', 'dele_cgc'],
                'rol' => ['Presidente', 'Presidenta', 'Delegado presidente CGC'],
            ]);
        $command = $query->createCommand();
        $idNkPlanes = array_map('intval', $command->queryColumn());

        return $idNkPlanes;
    }

    /**
     * Devuelve los presidentes de la comisión de garantía de todas las titulaciones.
     */
    public function getPresidentes($language)
    {
        $anyo_academico = Calendario::getAnyoAcademico();
        $query = new Query();
        $query->select(
            'agente.estudio_id_nk, estudio_lang.nombre AS nombreEstudio,'
            . ' centro_lang.nombre AS nombreCentro, plan_id_nk, agente.nombre,'
            . ' agente.apellido1, agente.apellido2, LOWER(agente.email) AS email, agente.nip'
        )
            ->from('agente')
            ->innerJoin('plan', "agente.plan_id_nk = plan.id_nk AND plan.anyo_academico = $anyo_academico")
            ->leftJoin('estudio_lang', 'agente.estudio_id = estudio_lang.estudio_id')
            ->leftJoin('centro_lang', 'agente.centro_id = centro_lang.centro_id')
            ->where(['agente.rol' => ['Presidente', 'Presidenta']])
            ->andWhere(['agente.comision_id' => 'G'])  // Comisión de Garantía
            ->andWhere(['estudio_lang.language' => $language])
            ->andWhere(['centro_lang.language' => $language])
            ->andWhere(['plan.activo' => 1]);
        $command = $query->createCommand();
        // die($command->rawSql);  // Returns the raw SQL by inserting parameter values into the corresponding placeholders
        $presidentes = $command->queryAll();

        return $presidentes;
    }

    /**
     * Devuelve la dirección de correo electrónico del presidente/a de la CGC de un plan.
     */
    public static function getPresidenteCgc($plan_id_nk)
    {
        $presidente = Agente::find()
            ->where([
                'plan_id_nk' => $plan_id_nk,
                'comision_id' => 'G',
                'rol' => ['Presidente', 'Presidenta'],
            ])->one();
        if (!$presidente) {
            return null;
        }
        return $presidente->email;
    }

    /**
     * Devuelve los coordinadores del estudio indicado, clasificados por centro.
     */
    public static function getCoordinadores($estudio_id)
    {
        $coordinadores = self::find()
            ->where(['estudio_id' => $estudio_id, 'comision_id' => 'O'])
            ->all();

        $coordinadores_por_centro = [];
        foreach ($coordinadores as $coordinador) {
            $coordinadores_por_centro[$coordinador->centro_id] = [
                'nombre_completo' => sprintf(
                    '%s %s %s',
                    $coordinador->nombre,
                    $coordinador->apellido1,
                    $coordinador->apellido2
                ),
                'email' => strtolower($coordinador->email),
                'nip' => $coordinador->nip,
            ];
        }

        return $coordinadores_por_centro;
    }
}
