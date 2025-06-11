<?php
/**
 * Fragmento que muestra información de un estudio en un centro.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use app\models\Centro;
use yii\helpers\Html;

foreach ($planes_por_centro as $centro_id => $planes) :
    $centro = Centro::findOne($centro_id); ?>

    <div class="col-sm-4 col-md-4">
        <h4>
            <?php echo Html::a($centro->nombre, $centro->url); ?>
            <span class="glyphicon glyphicon-link"></span>
        </h4>

        <p>
        <?php
        printf('%s<br>', $centro->direccion);
        printf('%s<br>', $centro->municipio);
        printf(
            '%s: %s<br>',
            Yii::t('cati', 'Correo electrónico'),
            # Html::mailto('docto@unizar.es', 'docto@unizar.es')
            Html::a(Yii::t('cati', 'Consultar aquí'), 'https://escueladoctorado.unizar.es/sites/escueladoctorado/files/users/docto/oferta-pd-web.pdf')
        );
        printf(
            '%s: %s %s<br>',
            Yii::t('cati', 'Código del plan'),
            $planes[0]->id_nk,
            $planes[0]->es_interuniversitario ? '(' . Yii::t('cati', 'Programa interuniversitario') . ')' : ''
        );
        printf(
            '%s: %s<br>',
            Yii::t('cati', 'Coordinador'),
            Html::mailto(
                $planes[0]->nombre_coordinador,
                $planes[0]->email_coordinador
            )
        );
        if ($planes[0]->url_web_plan) {
            echo Html::a(
                Yii::t('doct', 'Web específica del programa'),
                $planes[0]->url_web_plan
            );
        } ?><br>
        </p>
    </div>
<?php
endforeach;
