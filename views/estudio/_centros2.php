<?php

use app\models\Centro;
use yii\helpers\Html;

foreach ($planes_por_centro as $centro_id => $planes) {
    $centro = Centro::findOne($centro_id); ?>

    <div class="col-sm-4 col-md-4">
        <h4><?php echo Html::a(Html::encode($centro->nombre), $centro->url); ?>
            <span class="glyphicon glyphicon-link"></span></h4>

        <p><?php echo Html::encode($centro->direccion); ?><br>
            <?php echo Html::encode($centro->municipio); ?><br>
            <?php
            if (trim($centro->telefono)) {
                printf('%s: %s<br>', Yii::t('cati', 'Tel'), Html::encode($centro->telefono));
            } ?>
            <?php
            echo Yii::t('cati', 'Coordinación') . ': ';
            // En la tabla de planes, procedente de ODS, el nombre del coordinador aparece todo en mayúsculas.
            // En la tabla de agentes del sistema, procedente de People, aparece normal.
            // Si el email de las 2 tablas coincide, tomo el nombre de Agentes, y si no, de Planes.
            /*
            echo Html::mailto(
                (isset($coordinadores[$centro->id]['email'])
                  and $coordinadores[$centro->id]['email'] == $planes[0]->email_coordinador)
                  ? Html::encode($coordinadores[$centro->id]['nombre_completo'])
                  : Html::encode($planes[0]->nombre_coordinador),
                $planes[0]->email_coordinador ? $planes[0]->email_coordinador : ''
            ) . "<br>\n";
            */
            // Desde la EINA llevan un tiempo pidiendo que en el portal de titulaciones, cuando se ve el nombre del
            // coordinador y se pincha en él para enviarle un correo electrónico, no le llegue a su correo personal,
            // sino a un correo institucional que han creado para cada título de grado o máster.
            // Este dato está informado en People.
            echo Html::mailto(
                isset($coordinadores[$centro->id]['nombre_completo']) ? Html::encode($coordinadores[$centro->id]['nombre_completo']) : Html::encode($planes[0]->nombre_coordinador),
                $coordinadores[$centro->id]['email'] ?? $planes[0]->email_coordinador
            ) . "<br>\n";

            if ($planes[0]->url_web_plan) {
            echo Html::a(
                Yii::t('doct', 'Web específica del programa'),
                $planes[0]->url_web_plan
            );
        }
            ?>
        </p>

        <ul class="centros">
        <?php
        foreach ($planes as $plan) {
            // Hasta el curso 2019-20, el plan 415 era ficticio, creado para abarcar a todas las especialidades, que se habían creado como planes.
            if (415 == $plan->id_nk) {
                continue;
            }

            $texto = sprintf(Yii::t('cati', 'Asignaturas del plan %d'), $plan->id_nk);
            // XXX SMELLS Hasta el curso 2019-20 para el estudio 659 se creaban planes para cada especialidad.
            // Le damos a cada plan el nombre de la especialidad en vez de mostrar su número.
            if (659 == $estudio->id_nk and $estudio->anyo_academico < 2019) {
                require_once '_nombres_659.php';
                $texto = \yii\helpers\ArrayHelper::getValue($nombres, $plan->id_nk, "Desconocido {$plan->id_nk}");
            }

            if (strpos($texto, 'Desconocido') === false) {
                if ($plan->nuevo) {
                    $texto .= sprintf('<span>(%s)</span>', Yii::t('cati', 'nuevo'));
                }
                if ($plan->en_extincion) {
                    $texto .= sprintf('<span>(%s)</span>', Yii::t('cati', 'en extinción'));
                    echo "<li class='centros-asig' style='background: #6595C0;'>";
                } else {
                    echo '<li class="centros-asig">';
                }

                echo Html::a(
                    $texto,
                    [
                        'estudio/asignaturas',
                        'anyo_academico' => $anyo_academico,
                        'estudio_id' => $estudio->id,
                        'centro_id' => $centro->id,
                        'plan_id_nk' => $plan->id_nk,
                        'sort' => 'curso',
                    ]
                );
            }
            echo "</li>\n";

            if (isset($plan->url_horarios)) {
                echo '<li>' . Html::a(
                    Yii::t('cati', 'Horarios'),
                    $plan->url_horarios
                ) . " <span class='glyphicon glyphicon-link'></span></li>\n";
            } /* else {
                echo '<br>';
            } */

            echo '<li>' . Html::a(
                Yii::t('cati', 'Tutorías'),
                'https://directorio.unizar.es/#/tutoria?colectivo=PDI&codCentro=' . $centro->id
            ). " <span class='glyphicon glyphicon-link'></span></li>\n";
        } ?>
        </ul>
    </div>
<?php
} //end foreach
