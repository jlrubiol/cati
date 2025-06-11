<?php
/**
 * Fragmento de vista con la tabla de movilidad agrupada por macroáreas.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */

use app\models\DoctoradoMacroarea;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/*
0. id
1. cod_rama_conocimiento
2. ano_academico

### _acceso_admision_matricula ###
3. plazas_ofertadas                 - 1.1. Oferta de plazas
4. num_solicitudes                  - 1.2. Demanda
5. alumnos_nuevo_ingreso            - 1.3. Estudiantes matriculados de nuevo ingreso
6. porc_est_previo_nouz             - 1.4. Porcentaje de estudiantes de nuevo ingreso procedentes de estudios de máster de otras universidades
7. porc_ni_comp_formacion           - 1.5. Porcentaje de estudiantes de nuevo ingreso que han requerido complementos formativos
8. porc_ni_tiempo_parcial           - 1.6. Porcentaje de estudiantes de nuevo ingreso matriculados a tiempo parcial
9. alumnos_matriculados             - 1.7. Número total de estudiantes matriculados
10. porc_matr_extrajeros            - 1.8. Porcentaje de estudiantes extranjeros matriculados
11. porc_alumnos_beca               - 1.9. Porcentaje de estudiantes con beca o contrato predoctoral
12. porc_alumnos_beca_distinta      - 1.9.b. Porcentaje de estudiantes con beca distinta de las contempladas en indicador 1.9
13. porc_matri_tiempo_parcial       - 1.10. Porcentaje de estudiantes matriculados a tiempo parcial

### _transversales ###
14. alumnos_act_transv              * 2.3.1. Número de estudiantes que han realizado actividades transversales
15. cursos_act_transv               * 2.3.2. Actividades transversales de la EDUZ: cursos

### _formacion ###
16. numero_tesis_tiempo_completo    - 6.1. Número de tesis defendidas a tiempo completo
17. numero_tesis_tiempo_parcial     - 6.2. Número de tesis defendidas a tiempo parcial
18. duracion_media_tiempo_completo  - 6.3. Duración media del programa de doctorado a tiempo completo
19. duracion_media_tiempo_parcial   - 6.4. Duración media del programa de doctorado a tiempo parcial
20. porc_abandono                   - 6.5. Porcentaje de abandono del programa de doctorado
21. porc_tesis_no_pri_prorroga      - 6.6.1. Porcentaje de tesis defendidas que no han requerido una primera prórroga de estudios
22. porc_tesis_no_seg_prorroga      - 6.6.2. Porcentaje de tesis defendidas que no han requerido una segunda prórroga de estudios
23. porc_tesis_cum_laude            - 6.7. Porcentaje de tesis con la calificación de Cum Laude
24. porc_tesis_men_internacional    - 6.8. Porcentaje de doctores con mención internacional
25. porc_tesis_men_doc_industrial   - 6.9. Porcentaje de doctores con mención de doctorado industrial
26. porc_tesis_cotutela             - 6.10. Porcentaje de doctores en cotutela de tesis
27. num_medio_resultados_tesis      * 6.11. Número medio de resultados científicos de las tesis doctorales

### _movilidad ###
28. porc_alumnos_mov_out_ano        * 3.1. Porcentaje de estudiantes del programa de doctorado que han realizado estancias de investigación en el año
29. porc_alumnos_mov_out_gen        * 3.2. Porcentaje de estudiantes del programa de doctorado que han realizado estancias de investigación

### _personal_academico ###
30. numero_profesores               - 4.1. Número total de directores y tutores de tesis
31. numero_profesores_uz            - 4.1.1. Número total de directores y tutores con vinculación contractual con la Universidad de Zaragoza
32. numero_profesores_nouz          - 4.1.2. Número total de directores y tutores sin vinculación contractual con la Universidad de Zaragoza
33. num_sexenios_profesores         - 4.2. Experiencia investigadora
34. porc_sexenios_vivos             - 4.3. Porcentaje de sexenios vivos
35. porc_prof_tiempo_completo       - 4.4. Porcentaje de dedicación
36. numero_expertos_int_trib        * 4.5. Numerador: Número de miembros internacionales en los tribunales
37. numero_miembros_trib            * 4.5. Denominador: Número de miembros tribunales de tesis defendidas en el curso objeto del estudio
38. numero_directores_tesis_leidas  - 4.6. Número de directores de tesis leídas
39. porc_dir_tes_le_sexenios_vivos  - 4.7. Sexenios vivos de los directores de tesis leídas
40. numero_proy_inter_vivos         - 4.8. Número de proyectos internacionales vivos en el año
41. numero_proy_nac_vivos           - 4.9. Número de proyectos nacionales vivos en el año
42. numero_publ_indexadas           - 4.10. Número de publicaciones indexadas en el año
43. numero_publ_no_indexadas        - 4.11. Número de publicaciones no indexadas en el año

44. tasa_satisfaccion_estudiantes
45. media_satisfaccion_estudiantes
46. tasa_satisfaccion_tutores
47. media_satisfaccion_tutores
48. tasa_satisfaccion_egresados
49. media_satisfaccion_egresados
50. fecha_carga
51. num_programas                   - Número de programas de doctorado
*/

$ramas = array_slice($datos[1], 2, 6);  // cod_rama_conocimiento

# 30 (numero_profesores) - 43 (numero_publ_no_indexadas)
$datos_tabla = array_slice($datos, 30, 14);

// Estos datos son introducidos por la Escuela de Doctorado.
// Si todavía no ha introducido el dato, ponemos un interrogante.
for ($i = 6; $i <= 7; $i++) {
    $datos_tabla[$i] = array_map(
        function ($dato) {
            return ($dato === null) ? '?' : $dato;
        },
        $datos_tabla[$i]
    );
}

// Preparamos el indicador 4.5
$fila = ['porc_expertos_internac_trib', 'porc_expertos_internac_trib'];
for ($i = 2; $i <= 7; $i++) {
    $fila[$i] = sprintf(
        '%s/%s',
        $datos_tabla[6][$i] ?? '?',
        $datos_tabla[7][$i] ?? '?'
    );
}
$datos_tabla = array_merge(array_slice($datos_tabla, 0, 6), [6 => $fila], array_slice($datos_tabla, 8, 6));

if ($mostrar_botones) {
    $botones = ['', ''];
    foreach ($ramas as $rama_id) {
        $botones[] = Html::a(
            '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('gestion', 'Editar datos'),
            ['doctorado-macroarea/editar-datos', 'anyo' => $anyo, 'rama_id' => $rama_id],
            [
                'id' => "editar-datos-{$rama_id}",
                'class' => 'btn btn-info btn-xs',  // Button
                'title' => Yii::t('gestion', 'Editar los datos'),
            ]
        );
    }
    array_push($datos_tabla, $botones);
}

$dataProvider = new ArrayDataProvider(
    [
        'allModels' => $datos_tabla,
        'pagination' => false,  // ['pageSize' => 10],
    ]
);

$model = new DoctoradoMacroarea();

echo "<div class='table-responsive'>";
echo GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 1,
                'label' => Yii::t('cati', 'Concepto'),
                'value' => function ($registro) use ($model) {
                    return '<strong>' . $model->getAttributeLabel($registro[1]) . '</strong>';
                },
                'format' => 'html',
                'contentOptions' => function ($model, $key, $index, $column) use ($descripciones) {
                    return [
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                        'title' => ArrayHelper::getValue($descripciones, $model[0]),
                    ];
                },
            ], [
                'attribute' => 2,
                'contentOptions' => ['style' => 'text-align: right;'],
                'format' => 'html',  // decimal',
                'headerOptions' => ['style' => 'text-align: right;'],
                'label' => Yii::t('cati', 'Total'),
            ], [
                'attribute' => 3,
                'contentOptions' => ['style' => 'text-align: right;'],
                'format' => 'html',  // decimal',
                'headerOptions' => ['style' => 'text-align: right;'],
                'label' => Yii::t('cati', 'Artes y Humanidades'),
            ], [
                'attribute' => 4,
                'contentOptions' => ['style' => 'text-align: right;'],
                'format' => 'html',  // decimal',
                'headerOptions' => ['style' => 'text-align: right;'],
                'label' => Yii::t('cati', 'Ciencias Sociales y Jurídicas'),
            ], [
                'attribute' => 5,
                'contentOptions' => ['style' => 'text-align: right;'],
                'format' => 'html',  // decimal',
                'headerOptions' => ['style' => 'text-align: right;'],
                'label' => Yii::t('cati', 'Ciencias de la Salud'),
            ], [
                'attribute' => 6,
                'contentOptions' => ['style' => 'text-align: right;'],
                'format' => 'html',  // decimal',
                'headerOptions' => ['style' => 'text-align: right;'],
                'label' => Yii::t('cati', 'Ingeniería y Arquitectura'),
            ], [
                'attribute' => 7,
                'contentOptions' => ['style' => 'text-align: right;'],
                'format' => 'html',  // decimal',
                'headerOptions' => ['style' => 'text-align: right;'],
                'label' => Yii::t('cati', 'Ciencias'),
            ],
        ],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '—'],
        'options' => ['class' => 'cabecera-azul'],
        'summary' => false,  // Do not show `Showing 1-19 of 19 items'.
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        // 'caption' => sprintf("<p style='font-size: 140%%;'>%s</p>", $caption),
        // 'captionOptions' => ['style' => 'text-align: center;'],
    ]
);
echo '</div>';

echo "<p>Nota indicadores 4.10 y 4.11: Hasta campaña 2023 el período considerado es desde septiembre a agosto; a partir de la campaña 2024 (curso 2023/2024) la información se refiere al año natural de inicio del curso académico objeto del informe.</p>";
