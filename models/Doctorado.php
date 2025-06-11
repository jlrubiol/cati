<?php

namespace app\models;

use app\models\base\Doctorado as BaseDoctorado;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "DATUZ_doctorado".
 * Los datos proceden de la transformación `doctorado/tit_acreditacion_doctorado_plan`
 * y se cargan desde el menú Gestión -> Doctorado -> Otros -> Actualizar datos académicos de Doctorado
 *
 * Los datos de la tabla de satisfacción y egreso corresponden al modelo `Encuestas.php` (tabla `DATUZ_encuestas`).
 */
class Doctorado extends BaseDoctorado
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
                'cod_estudio' => Yii::t('models', 'Código del estudio'),
                'cod_plan' => Yii::t('models', 'Código del plan'),
                'cod_centro' => Yii::t('models', 'Código del centro'),
                'ano_academico' => Yii::t('models', 'Año académico'),

                // Descripciones de los indicadores en `views/informe/doct/_descripciones.php`

                // ACCESO, ADMISIÓN Y MATRÍCULA
                'plazas_ofertadas' => Yii::t('models', '1.1. Oferta de plazas'),
                'num_solicitudes' => Yii::t('models', '1.2. Demanda'),
                'alumnos_nuevo_ingreso' => Yii::t('models', '1.3. Estudiantes matriculados/as de nuevo ingreso'),
                'porc_est_previo_nouz' => Yii::t(
                    'models',
                    '1.4. Porcentaje de estudiantes de nuevo ingreso procedentes de'
                    . ' estudios de acceso a doctorado de otras universidades'
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
                'porc_matr_extrajeros' => Yii::t('models', '1.8. Porcentaje de estudiantes extranjeros/as matriculados/as'),
                'porc_alumnos_beca' => Yii::t(
                    'models',
                    '1.9. Porcentaje de estudiantes con beca o contrato predoctoral'
                ),
                'porc_alumnos_beca_distinta' => Yii::t(
                    'models',
                    '1.9.b. Porcentaje de estudiantes con beca distinta de las contempladas en indicador 1.9'
                ),
                'porc_matri_tiempo_parcial' => Yii::t(
                    'models',
                    '1.10. Porcentaje de estudiantes matriculados/as a tiempo parcial'
                ),

                // ACTIVIDADES DE FORMACIÓN TRANSVERSALES
                'porc_alumnos_act_transv' => Yii::t(
                    'models',
                    '2.3.1. Actividades transversales de la EDUZ: estudiantes'
                ),
                'alumnos_act_transv' => Yii::t(
                    'models',
                    '2.3.1. Numerador: Número de estudiantes que han realizado actividades transversales'
                ),

                // MOVILIDAD
                'porc_alumnos_mov_out_ano' => Yii::t(
                    'models',
                    '3.1. Porcentaje de estudiantes del PD a los que se le autoriza realizar estancias internacionales de investigación en el curso académico objeto del informe',
                ),
                'porc_alumnos_mov_out_gen' => Yii::t(
                    'models',
                    '3.2. Porcentaje de estudiantes del programa de doctorado que'
                    . ' han realizado estancias de investigación'
                ),

                // PERSONAL ACADÉMICO
                'numero_profesores' => Yii::t('models', '4.1. Número total de directores/as y tutores/as de tesis'),
                'numero_profesores_uz' => Yii::t(
                    'models',
                    '4.1.1. Número total de directores/as y tutores/as vinculados/as estatutaria o contractualmente con la UZ.'
                ),
                'numero_profesores_nouz' => Yii::t(
                    'models',
                    '4.1.2. Número total de directores/as y tutores/as no vinculados/as estatutaria ni contractualmente con la UZ.'
                ),
                'num_sexenios_profesores' => Yii::t('models', '4.2. Experiencia investigadora'),
                'porc_sexenios_vivos' => Yii::t('models', '4.3. Porcentaje de sexenios vivos'),
                'porc_prof_tiempo_completo' => Yii::t('models', '4.4. Porcentaje de dedicación'),
                'porc_expertos_internac_trib' => Yii::t('models', '4.5. Presencia de expertos/as internacionales en tribunales de tesis'),
                'numero_expertos_int_trib' => Yii::t(
                    'models',
                    '4.5. Numerador: Número de miembros internacionales en los tribunales'
                ),
                'numero_miembros_trib' => Yii::t(
                    'models',
                    '4.5. Denominador: Número de miembros tribunales de tesis defendidas en el curso objeto del estudio'
                ),
                'numero_directores_tesis_leidas' => Yii::t('models', '4.6. Número de directores/as de tesis defendidas'),
                'porc_dir_tes_le_sexenios_vivos' => Yii::t(
                    'models',
                    '4.7. Sexenios vivos de los directores/as de tesis defendidas.'
                ),
                'numero_proy_inter_vivos' => Yii::t(
                    'models',
                    '4.8. Número de proyectos internacionales vivos en el año'
                ),
                'numero_proy_nac_vivos' => Yii::t('models', '4.9. Número de proyectos nacionales vivos en el año'),
                'numero_publ_indexadas' => Yii::t('models', '4.10. Número de publicaciones indexadas en el año'),
                'numero_redondeado_publ_indexadas' => Yii::t('models', '4.10. Número de publicaciones indexadas en el año'),
                'numero_publ_no_indexadas' => Yii::t('models', '4.11. Número de publicaciones no indexadas en el año'),
                'numero_redondeado_publ_no_indexadas' => Yii::t('models', '4.11. Número de publicaciones no indexadas en el año'),

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
                'num_medio_resultados_tesis2' => Yii::t(
                    'models',
                    '6.11. Número medio de resultados científicos de las tesis doctorales'
                ),

                'numero_alu_encuesta_global_1' => Yii::t(
                    'models',
                    '7.1.1. Número de estudiantes que en la encuesta de satisfacción han valorado globalmente el'
                    . ' programa con una puntuación de 1 sobre 5 en relación con el total de estudiantes que han'
                    . ' respondido a la encuesta'
                ),
                'numero_alu_encuesta_global_2' => Yii::t(
                    'models',
                    '7.1.2. Número de estudiantes que en la encuesta de satisfacción han valorado globalmente el'
                    . ' programa con una puntuación de 2 sobre 5 en relación con el total de estudiantes que han'
                    . ' respondido a la encuesta'
                ),
                'numero_alu_encuesta_global_3' => Yii::t(
                    'models',
                    '7.1.3. Número de estudiantes que en la encuesta de satisfacción han valorado globalmente el'
                    . ' programa con una puntuación de 3 sobre 5 en relación con el total de estudiantes que han'
                    . ' respondido a la encuesta'
                ),
                'numero_alu_encuesta_global_4' => Yii::t(
                    'models',
                    '7.1.4. Número de estudiantes que en la encuesta de satisfacción han valorado globalmente el'
                    . ' programa con una puntuación de 4 sobre 5 en relación con el total de estudiantes que han'
                    . ' respondido a la encuesta'
                ),
                'numero_alu_encuesta_global_5' => Yii::t(
                    'models',
                    '7.1.5. Número de estudiantes que en la encuesta de satisfacción han valorado globalmente el'
                    . ' programa con una puntuación de 5 sobre 5 en relación con el total de estudiantes que han'
                    . ' respondido a la encuesta'
                ),
                'numero_prof_encuesta_global_1' => Yii::t(
                    'models',
                    '7.2.1. Número de directores y tutores que en la encuesta de satisfacción han valorado globalmente'
                    . ' el programa con una puntuación de 1 sobre 5 en relación con el total de directores y tutores'
                    . ' que han respondido a la encuesta'
                ),
                'numero_prof_encuesta_global_2' => Yii::t(
                    'models',
                    '7.2.2. Número de directores y tutores que en la encuesta de satisfacción han valorado globalmente'
                    . ' el programa con una puntuación de 2 sobre 5 en relación con el total de directores y tutores'
                    . ' que han respondido a la encuesta'
                ),
                'numero_prof_encuesta_global_3' => Yii::t(
                    'models',
                    '7.2.3. Número de directores y tutores que en la encuesta de satisfacción han valorado globalmente'
                    . ' el programa con una puntuación de 3 sobre 5 en relación con el total de directores y tutores'
                    . ' que han respondido a la encuesta'
                ),
                'numero_prof_encuesta_global_4' => Yii::t(
                    'models',
                    '7.2.4. Número de directores y tutores que en la encuesta de satisfacción han valorado globalmente'
                    . ' el programa con una puntuación de 4 sobre 5 en relación con el total de directores y tutores'
                    . ' que han respondido a la encuesta'
                ),
                'numero_prof_encuesta_global_5' => Yii::t(
                    'models',
                    '7.2.5. Número de directores y tutores que en la encuesta de satisfacción han valorado globalmente'
                    . ' el programa con una puntuación de 5 sobre 5 en relación con el total de directores y tutores'
                    . ' que han respondido a la encuesta'
                ),
                'fecha_carga' => Yii::t('models', 'Fecha de carga'),
            ]
        );
    }

    public function getPorc_alumnos_act_transv()
    {
        return sprintf(
            '%s/%d',
            $this->alumnos_act_transv !== null ? $this->alumnos_act_transv : '?',
            $this->alumnos_matriculados
        );
    }

    public function getPorc_expertos_internac_trib()
    {
        return sprintf(
            '%s/%s',
            ($this->numero_expertos_int_trib !== null) ? $this->numero_expertos_int_trib : '?',
            ($this->numero_miembros_trib !== null) ? $this->numero_miembros_trib: '?'
        );
    }

    public function getNum_medio_resultados_tesis2()
    {
        return $this->num_medio_resultados_tesis !== null ? $this->num_medio_resultados_tesis : '?';
    }

    public function getNumero_redondeado_publ_indexadas()
    {
        return round($this->numero_publ_indexadas);
    }

    public function getNumero_redondeado_publ_no_indexadas()
    {
        return round($this->numero_publ_no_indexadas);
    }
}
