<h2><?php echo Yii::t('doct', 'Anexo: Descripción de los indicadores'); ?></h2>

<dl>
<?php
$model = new \app\models\DoctoradoMacroarea();
foreach ($descripciones as $clave => $valor) {
    printf("<dt>%s</dt>\n", $model->getAttributeLabel($clave));
    printf("<dd>%s<br><br></dd>\n", $valor);
}
?>
</dl>