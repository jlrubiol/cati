<?php
/**
 * Modelo de la tabla plan_respuesta_lang.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */

namespace app\models;

use app\models\base\PlanRespuestaLang as BasePlanRespuestaLang;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "plan_respuesta_lang".
 */
class PlanRespuestaLang extends BasePlanRespuestaLang
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'language' => Yii::t('models', 'Idioma'),
            'apartado_memoria' => Yii::t('models', 'Apartado de la memoria de verificación'),
            'titulo' => Yii::t('models', 'Título'),
            'descripcion_breve' => Yii::t('models', 'Descripcion breve'),
            'descripcion_amplia' => Yii::t('models', 'Descripción'),
            'responsable_accion' => Yii::t('models', 'Responsable de la accion y seguimiento'),
            'inicio' => Yii::t('models', 'Fecha de inicio'),
            'final' => Yii::t('models', 'Fecha final'),
            'responsable_competente' => Yii::t('models', 'Responsable competente'),
            'justificacion' => Yii::t('models', 'Justificación'),
            'justificacion_breve' => Yii::t('models', 'Justificación'),
            'nivel' => Yii::t('models', 'Nivel'),
            'fecha' => Yii::t('models', 'Fecha'),
            'problema' => Yii::t('models', 'Problema diagnosticado'),
            'objetivo' => Yii::t('models', 'Objetivos de mejora'),
            'acciones' => Yii::t('models', 'Acciones propuestas'),
            'plazo_implantacion' => Yii::t('models', 'Plazo de implantación'),
            'indicador' => Yii::t('models', 'Indicador'),
            'valores_a_alcanzar' => Yii::t('models', 'Valores a alcanzar'),
            'valores_alcanzados' => Yii::t('models', 'Valores alcanzados'),
            'necesidad_detectada' => Yii::t('models', 'Necesidad detectada'),
            'observaciones' => Yii::t('models', 'Observaciones'),
        ];
    }
}
