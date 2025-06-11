<?php
/**
 * Modelo de la tabla DATUZ_doctorado_macroarea.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */

namespace app\models;

use Yii;
use app\models\base\DoctoradoMacroarea as BaseDoctoradoMacroarea;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "DATUZ_doctorado_macroarea".
 */
class DoctoradoMacroarea extends BaseDoctoradoMacroarea
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

    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'id' => Yii::t('models', 'ID'),
                'cod_rama_conocimiento' => Yii::t('models', 'Macroárea'),
                'ano_academico' => Yii::t('models', 'Año académico'),

                // ACCESO, ADMISIÓN Y MATRÍCULA
                'plazas_ofertadas' => Yii::t('models', '1.1. Oferta de plazas'),
                'num_solicitudes' => Yii::t('models', '1.2. Demanda'),
                'alumnos_nuevo_ingreso' => Yii::t('models', '1.3. Estudiantes matriculados/as de nuevo ingreso'),
                'porc_est_previo_nouz' => Yii::t(
                    'models',
                    '1.4. Porcentaje de estudiantes de nuevo ingreso procedentes de estudios de máster de otras universidades'
                ),
                'porc_ni_comp_formacion' => Yii::t(
                    'models',
                    '1.5. Porcentaje de estudiantes de nuevo ingreso que han requerido complementos formativos'
                ),
                'porc_ni_tiempo_parcial' => Yii::t(
                    'models',
                    '1.6. Porcentaje de estudiantes de nuevo ingreso matriculados/as a tiempo parcial'
                ),
                'alumnos_matriculados' => Yii::t('models', '1.7. Número total de estudiantes matriculados/as'),
                'porc_matr_extrajeros' => Yii::t('models', '1.8. Porcentaje de estudiantes extranjeros matriculados/as'),
                'porc_alumnos_beca' => Yii::t(
                    'models',
                    '1.9. Porcentaje de estudiantes con beca o contrato predoctoral'
                ),
                'porc_alumnos_beca_distinta' => Yii::t(
                    'models',
                    '1.9.b. Porcentaje de estudiantes con beca distinta de las contempladas en indicador 1.9'
                ),
                'porc_matri_tiempo_parcial' => Yii::t('models', '1.10. Porcentaje de estudiantes matriculados/as a tiempo parcial'),

                // ACTIVIDADES DE FORMACIÓN TRANSVERSALES
                'porc_alumnos_act_transv' => Yii::t('models', '2.3.1. Actividades transversales de la EDUZ: estudiantes'),
                'alumnos_act_transv' => Yii::t('models', '2.3.1. Número de estudiantes que han realizado actividades transversales'),

                'porc_expertos_internac_trib' => Yii::t('models', '4.5. Presencia de expertos/as internacionales'),  // No en BD, sólo para views/informe/iced/_personal_academico
                'numero_directores_tesis_leidas' => Yii::t('models', '4.6. Número de directores/as de tesis defendidas'),
                'porc_dir_tes_le_sexenios_vivos' => Yii::t(
                    'models',
                    '4.7. Sexenios vivos de los directores/as de tesis defendidas.'
                ),

                // RESULTADOS DEL APRENDIZAJE
                'numero_tesis_tiempo_completo' => Yii::t('models', '6.1. Número de tesis defendidas a tiempo completo'),
                'numero_tesis_tiempo_parcial' => Yii::t('models', '6.2. Número de tesis defendidas a tiempo parcial'),
                'duracion_media_tiempo_completo' => Yii::t(
                    'models',
                    '6.3. Duración media del programa de doctorado a tiempo completo'
                ),
                'duracion_media_tiempo_parcial' => Yii::t(
                    'models',
                    '6.4. Duración media del programa de doctorado a tiempo parcial'
                ),
                'porc_abandono' => Yii::t('models', '6.5. Porcentaje de abandono del programa de doctorado'),
                'porc_tesis_no_pri_prorroga' => Yii::t(
                    'models',
                    '6.6.1. Porcentaje de tesis defendidas que no han requerido una primera prórroga de estudios'
                ),
                'porc_tesis_no_seg_prorroga' => Yii::t(
                    'models',
                    '6.6.2. Porcentaje de tesis defendidas que no han requerido una segunda prórroga de estudios'
                ),
                'porc_tesis_cum_laude' => Yii::t('models', '6.7. Porcentaje de tesis con mención Cum Laude'),
                'porc_tesis_men_internacional' => Yii::t(
                    'models',
                    '6.8. Porcentaje de tesis con mención internacional'
                ),
                'porc_tesis_men_doc_industrial' => Yii::t(
                    'models',
                    '6.9. Porcentaje de tesis con mención industrial'
                ),
                'porc_tesis_cotutela' => Yii::t('models', '6.10. Porcentaje de tesis en cotutela'),
                'num_medio_resultados_tesis' => Yii::t(
                    'models',
                    '6.11. Número medio de resultados científicos de las tesis'
                ),

                'fecha_carga' => Yii::t('models', 'Fecha de carga'),
                'num_programas' => Yii::t('models', 'Número de programas de doctorado'),
            ]
        );
    }

    public function getPorc_alumnos_act_transv()
    {
        return sprintf('%s/%d', $this->alumnos_act_transv ?: '—', $this->alumnos_matriculados);
    }
}
