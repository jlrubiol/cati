<?php

# Estas descripciones se usan en los globos de ayuda que se muestran
# al pasar el ratón sobre el indicador.
$descripciones = [
    // _acceso_admision_matricula
    // --------------------------
    // 1.1
    'plazas_ofertadas' => Yii::t(
        'doct',
        'Número de plazas que ofrece el PD'
    ),
    // 1.2
    'num_solicitudes' => Yii::t(
        'doct',
        'Número de solicitudes presentadas para acceder al PD',
    ),
    // 1.3
    'alumnos_nuevo_ingreso' => Yii::t(
        'doct',
        'Número de estudiantes de un PD que, por primera vez, han formalizado la matrícula.'
    ),
    // 1.4
    'porc_est_previo_nouz' => Yii::t(
        'doct',
        'Número de estudiantes de nuevo ingreso que no proceden de estudios de acceso a doctorado de la misma universidad en relación con el número total de estudiantes de nuevo ingreso matriculados en el PD.'
    ),
    // 1.5
    'porc_ni_comp_formacion' => Yii::t(
        'doct',
        'Número de estudiantes de nuevo ingreso que han requerido complementos formativos en relación con el número de estudiantes de nuevo ingreso en el PD.'
    ),
    // 1.6
    'porc_ni_tiempo_parcial' => Yii::t(
        'doct',
        'Número de estudiantes matriculados/as en primera tutela académica en régimen de tiempo parcial, en relación en el número total de estudiantes en primera tutela académica (nuevo ingreso).'
    ),
    'alumnos_matriculados' => Yii::t(
        'doct',
        'Número total de estudiantes que en el curso académico objeto del informe han formalizado su matrícula en el PD.'
    ),
    // 1.8
    'porc_matr_extrajeros' => Yii::t(
        'doct',
        'Número de estudiantes de nacionalidad extranjera matriculados/as en el PD en relación con el número total de estudiantes matriculados/as.'
    ),
    // 1.9
    'porc_alumnos_beca' => Yii::t(
        'doct',
        'Número total de estudiantes matriculados/as en el PD que en el curso académico objeto del informe están disfrutando de una beca o contrato predoctoral en relación con el número total de estudiantes matriculados/as.
        NOTA: Las becas incluidas en el indicador 1.9 son: Becas del Vicerrectorado de Investigación, Ayudas a la tutela académica de Doctorado, P.I.F: Contratados predoctorales FPI, FPU y DGA (gestionados por la UZ).'
    ),
    // 1.9.b
    'porc_alumnos_beca_distinta' => Yii::t(
        'doct',
        'Número total de estudiantes matriculados/as en el PD, que en el curso académico objeto del informe están disfrutando de una beca o contrato distintos de los contemplados en el indicador 1.9, en relación con el número total de estudiantes matriculados/as (estudiantes con otras becas: doctorados industriales, las resultantes de la acción Marie Slodowska Curie en sus diversas modalidades (ITN, IF), etc.).
        Quedan excluidos los trabajos remunerados, tanto a tiempo completo como a tiempo parcial, que el doctorando/a pueda tener con independencia de sus estudios de doctorado.',
    ),
    // 1.10
    'porc_matri_tiempo_parcial' => Yii::t(
        'doct',
        'Número de estudiantes matriculados/as en el PD durante el curso académico objeto del informe, en régimen de tiempo parcial, en relación en el número total de estudiantes matriculados/as durante dicho curso.'
    ),

    // _transversales
    // --------------
    // 2.3.1
    'porc_alumnos_act_transv' => Yii::t(
        'doct',
        'Número total de estudiantes del PD que en el curso académico objeto del informe hayan realizado, al menos, una actividad transversal en relación con el número total de estudiantes matriculados/as.'
    ),
    // 2.3.2
    'cursos_act_transv' => Yii::t(
        'doct',
        'Número total de actividades transversales que en el curso académico objeto del informe se hayan realizado por los estudiantes matriculados/as en el PD.'
    ),

    // _movilidad
    // ----------
    // 3.1
    'porc_alumnos_mov_out_ano' => Yii::t(
        'doct',
        'Número de estudiantes del PD a los que se autoriza iniciar, en el curso académico objeto del informe, estancias de investigación superiores a 30 días en centros de investigación o en universidades no españolas, en relación con el número total de estudiantes matriculados/as en el PD en dicho curso académico.'
    ),
    // 3.2
    'porc_alumnos_mov_out_gen' => Yii::t(
        'doct',
        'Número de estudiantes del programa de doctorado que han realizado estancias'
        . ' de investigación superiores a 30 días en centros de investigación o en'
        . ' otras universidades, en relación con el número total de estudiantes del'
        . ' programa de doctorado.'
    ),

    // _profesorado
    // ------------
    // 4.1
    'numero_profesores' => Yii::t(
        'doct',
        'Número total de directores/as y tutores/as de los estudiantes matriculados/as en el PD.'
    ),
    // 4.1.1
    'numero_profesores_uz' => Yii::t(
        'doct',
        'Número total de directores/as y tutores/as de los estudiantes matriculados/as en el PD, que tienen vinculación estatutaria o contractual con la UZ.'
    ),
    // 4.1.2
    'numero_profesores_nouz' => Yii::t(
        'doct',
        'Número total de directores/as y tutores/as de los estudiantes matriculados/as en el PD, que no tienen vinculación estatutaria o contractual con la UZ.'
    ),
    // 4.2
    'num_sexenios_profesores' => Yii::t(
        'doct',
        'Número de sexenios de investigación obtenidos por los directores/as y tutores/as que tienen vinculación estatutaria o contractual con la UZ.'
    ),
    // 4.3
    'porc_sexenios_vivos' => Yii::t(
        'doct',
        'Porcentaje de directores/as y tutores/as del PD que tienen vinculación estatutaria o contractual con la UZ, con sexenio vivo, en relación con el total de directores/as y tutores/as del PD que tienen vinculación estatutaria o contractual con la UZ.'
    ),
    // 4.4
    'porc_prof_tiempo_completo' => Yii::t(
        'doct',
        'Porcentaje de directores/as y tutores/as del PD que tienen vinculación estatutaria o contractual con la UZ, con dedicación a tiempo completo, en relación con el total de directores/as y tutores/as del PD que tienen vinculación estatutaria o contractual con la UZ.'
    ),
    // 4.5
    'porc_expertos_internac_trib' => Yii::t(
        'doct',
        'Número de miembros de los tribunales de tesis defendidas en el curso académico objeto del informe que pertenezcan a una institución extranjera, en relación con el número total de miembros de tribunales de tesis defendidas en el curso académico objeto del informe.'
    ),
    // 4.6
    'numero_directores_tesis_leidas' => Yii::t(
        'doct',
        'Número de directores/as que han dirigido tesis defendidas en el PD en el curso académico objeto del informe.'
    ),
    // 4.7
    'porc_dir_tes_le_sexenios_vivos' => Yii::t(
        'doct',
        'Porcentaje de directores/as de tesis defendidas en el PD en el curso académico objeto del informe que tienen vinculación estatutaria o contractual con la UZ, con sexenio vivo, en relación con el total de directores/as de tesis defendidas en el PD en el curso académico objeto del informe que tienen vinculación estatutaria o contractual con la UZ.'
    ),
    // 4.8
    'numero_proy_inter_vivos' => Yii::t(
        'doct',
        'Número de proyectos de directores/as y tutores/as del PD que tienen vinculación estatutaria o contractual con la UZ, financiados a cargo de programas u organismos internacionales, que estén vigentes en el curso académico objeto del informe.'
    ),
    // 4.9
    'numero_proy_nac_vivos' => Yii::t(
        'doct',
        'Número de proyectos de directores/as y tutores/as del PD que tengan vinculación estatutaria o contractual con la UZ, financiados a cargo de programas u organismos nacionales, que estén vigentes en el curso objeto del estudio.'
    ),
    // 4.10
    'numero_publ_indexadas' => Yii::t(
        'doct',
        'Número de publicaciones de directores/as y tutores/as del PD que tienen vinculación estatutaria o contractual con la UZ, en revistas incluidas en catálogos que asignen índices de calidad relativos (JCR).'
    ),
    'numero_redondeado_publ_indexadas' => Yii::t(
        'doct',
        'Número de publicaciones de directores/as y tutores/as del PD que tienen vinculación estatutaria o contractual con la UZ, en revistas incluidas en catálogos que asignen índices de calidad relativos (JCR).'
    ),
    // 4.11
    'numero_publ_no_indexadas' => Yii::t(
        'doct',
        'Número de publicaciones de directores/as y tutores/as del PD que tienen vinculación estatutaria o contractual con la UZ, en revistas no incluidas en catálogos que asignen índices de calidad relativos.'
    ),
    'numero_redondeado_publ_no_indexadas' => Yii::t(
        'doct',
        'Número de publicaciones de directores/as y tutores/as del PD que tienen vinculación estatutaria o contractual con la UZ, en revistas no incluidas en catálogos que asignen índices de calidad relativos.'
    ),


    // _formacion
    // ----------

    // 6.1
    'numero_tesis_tiempo_completo' => Yii::t(
        'doct',
        'Número de tesis defendidas en el curso académico objeto del informe por las personas matriculadas a tiempo completo en el PD durante dicho curso. '
    ),
    // 6.2
    'numero_tesis_tiempo_parcial' => Yii::t(
        'doct',
        'Número de tesis defendidas en el curso académico objeto del informe por las personas matriculadas a tiempo parcial en el PD durante dicho curso.'
    ),
    // 6.3
    'duracion_media_tiempo_completo' => Yii::t(
        'doct',
        'Número medio de años empleados por los/las estudiantes a tiempo completo que han defendido la tesis desde que comenzaron sus estudios de doctorado en el PD.'
    ),
    // 6.4
    'duracion_media_tiempo_parcial' => Yii::t(
        'doct',
        'Número medio de años empleados por los/las estudiantes a tiempo parcial que han defendido la tesis desde que comenzaron sus estudios de doctorado en el PD.'
    ),
    // 6.5
    'porc_abandono' => Yii::t(
        'doct',
        'Número de estudiantes que durante un curso académico objeto del informe, ni han formalizado la matrícula en el PD que cursaban ni han defendido la tesis en relación con el total de estudiantes que se podrían haber vuelto a matricular ese mismo curso.'
    ),
    // 6.6 Eficiencia del programa de doctorado
    // 6.6.1
    'porc_tesis_no_pri_prorroga' => Yii::t(
        'doct',
        'Número de tesis defendidas que no han requerido una primera prórroga de estudios en relación con el número total de tesis defendidas en el curso académico objeto del informe.'
    ),
    // 6.6.2
    'porc_tesis_no_seg_prorroga' => Yii::t(
        'doct',
        'Número de tesis defendidas que no han requerido una segunda prórroga de estudios en relación con el número total de tesis defendidas en el curso académico objeto del informe.'
    ),
    // 6.7
    'porc_tesis_cum_laude' => Yii::t(
        'doct',
        'Número de tesis con mención Cum Laude en el curso académico objeto del informe, en relación con el total de tesis defendidas en dicho curso.'
    ),
    // 6.8
    'porc_tesis_men_internacional' => Yii::t(
        'doct',
        'Número de tesis con mención internacional en el curso académico objeto del informe, en relación con el total de tesis defendidas en dicho curso.'
    ),
    // 6.9
    'porc_tesis_men_doc_industrial' => Yii::t(
        'doct',
        'Número de tesis con mención industrial en el curso académico objeto del informe, en relación con el total de tesis defendidas en dicho curso.'
    ),
    // 6.10
    'porc_tesis_cotutela' => Yii::t(
        'doct',
        'Número de tesis en cotutela en el curso académico objeto del informe, en relación con el total de tesis defendidas en dicho curso.'
    ),
    // 6.11
    'num_medio_resultados_tesis' => Yii::t(
        'doct',
        'Número de aportaciones, por tesis, aceptadas el día del depósito, incluyendo: artículos científicos en revistas indexadas, publicaciones (libros, capítulos de libros, etc.) con sistema de revisión por pares y patentes, en relación con las tesis depositadas.'
    ),
];
