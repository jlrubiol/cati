<?php
/**
 * Modelo de la tabla plan_respuesta.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */

namespace app\models;

use app\models\base\PlanRespuesta as BasePlanRespuesta;
use app\models\PlanPublicado;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "plan_respuesta".
 */
class PlanRespuesta extends BasePlanRespuesta
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                // custom behaviors
            ]
        );
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'apartado_memoria' => Yii::t('models', 'Apartado de la memoria de verificación'),
                'titulo' => Yii::t('models', 'Título'),
                'descripcion_breve' => Yii::t('models', 'Descripción de la acción'),
                'descripcion_amplia' => Yii::t('models', 'Descripción'),
                'responsable_accion' => Yii::t('models', 'Responsable del inicio de la acción'),
                'inicio' => Yii::t('models', 'Inicio'),
                'final' => Yii::t('models', 'Final'),
                'responsable_competente' => Yii::t('models', 'Responsable aprobación'),
                'justificacion' => Yii::t('models', 'Justificación'),
                'justificacion_breve' => Yii::t('models', 'Justificación'),
                'nivel' => Yii::t('models', 'Nivel'),
                'fecha' => Yii::t('models', 'Fecha'),
                'problema' => Yii::t('models', 'Problema diagnosticado'),
                'objetivo' => Yii::t('models', 'Objetivo de mejora'),
                'acciones' => Yii::t('models', 'Descripción de la acción'),
                'plazo_implantacion' => Yii::t('models', 'Plazo de implantación'),
                'indicador' => Yii::t('models', 'Indicadores'),
                'valores_a_alcanzar' => Yii::t('models', 'Valores a alcanzar'),
                'valores_alcanzados' => Yii::t('models', 'Valores alcanzados'),
                'cumplimiento' => Yii::t('models', '% cumplimiento acción'),
                'necesidad_detectada' => Yii::t('models', 'Necesidad detectada'),
                'ambito' => Yii::t('models', 'Ámbito de mejora'),
                'ambito_id' => Yii::t('models', 'Ámbito de mejora'),
                'responsable_aprobacion' => Yii::t('models', 'Responsable de aprobación'),
                'responsable_aprobacion_id' => Yii::t('models', 'Responsable de aprobación'),
                'plazo' => Yii::t('models', 'Plazo'),
                'plazo_id' => Yii::t('models', 'Plazo'),
                'apartado_memoria' => Yii::t('models', 'Apartado de la memoria'),
                'apartado_memoria_id' => Yii::t('models', 'Apartado de la memoria'),
                'tipo_modificacion' => Yii::t('models', 'Tipo de modificación'),
                'tipo_modificacion_id' => Yii::t('models', 'Tipo de modificación'),
                'seguimiento' => Yii::t('models', 'Seguimiento'),
                'seguimiento_id' => Yii::t('models', 'Seguimiento'),
                # Si se añaden nuevos atributos, modificar también
                # `views/plan-pregunta/_formulario.php`, `views/plan-mejora/_formulario.php`,
                # `views/plan-mejora/ver.php` y `views/gestion/extractos-paim-centro.php`.
                # Añadirlos también a las `rules` de más abajo.
                'estado' => Yii::t('models', 'Estado'),
                'estado_id' => Yii::t('models', 'Estado'),
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                // custom validation rules
                // Copiamos las reglas de models/base/PlanRespuestaLang.php,
                // porque Yii no las coge automágicamente.
                // Necesarias para la validación en los formularios de creación
                // y actualización, y para poder asignar masivamente los
                // atributos desde $_POST en el controlador.
                [['cumplimiento'], 'integer'],
                [['descripcion_amplia', 'justificacion'], 'string'],
                [['language'], 'string', 'max' => 5],
                [['apartado_memoria', 'titulo', 'responsable_accion', 'responsable_competente', 'valores_a_alcanzar', 'valores_alcanzados'], 'string', 'max' => 127],
                [['inicio', 'final'], 'string', 'max' => 18],
                [['nivel'], 'string', 'max' => 2],
                [['fecha'], 'string', 'max' => 24],
                [['problema', 'acciones', 'plazo_implantacion', 'indicador', 'necesidad_detectada'], 'string', 'max' => 191],
                [['descripcion_breve', 'objetivo', 'observaciones', 'justificacion_breve'], 'string', 'max' => 350],
            ]
        );
    }

    /**
     * Devuelve el número de elementos introducidos en cada plan para un año e idioma.
     */
    public function getContestadas($anyo, $language)
    {
        $tabla = (new Query())
            ->select(['estudio_id', 'cantidad' => 'COUNT(*)'])
            ->from(['pr' => 'plan_respuesta'])
            ->join('INNER JOIN', ['prl' => 'plan_respuesta_lang'], 'pr.id = prl.plan_respuesta_id')
            ->where(
                [
                    'anyo' => $anyo,
                    'language' => $language,
                ]
            )
            ->groupBy('estudio_id')
            ->all();

        $contestadas = [];
        foreach ($tabla as $fila) {
            $contestadas[$fila['estudio_id']] = $fila['cantidad'];
        }

        return $contestadas;
    }

    /*
     * El código es un campo automático que se genera al definir una nueva acción.
     * El objetivo es que todas las acciones puedan estar identificadas
     * con objeto de garantizar una trazabilidad los siguientes cursos.
     * Patrón decidido en el Vicerrectorado: año_estudio_nº automático (2020/125/1)
     */
    public function getCodigo()
    {
        return (string)$this->anyo . '/' . (string)$this->estudio_id_nk . '/' . (string)$this->id;
    }

    /**
     * Devuelve en qué versión se encuentra el PAIM al que pertenece esta respuesta.
     */
    public function getVersionPaim()
    {
        $pp = PlanPublicado::findOne([
            'estudio_id' => $this->estudio_id,
            'anyo' => $this->anyo,
            'language' => Yii::$app->request->cookies->getValue('language', 'es'),
        ]);

        return $pp->version;
    }
}
