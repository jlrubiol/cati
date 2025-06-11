<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

$anyo = $pregunta->anyo;
switch ($pregunta->tipo) {
    case 'grado-master':
        $nombre_lista = Yii::t('gestion', 'Informes de Grado y Máster');
        break;
    case 'doctorado':
        $nombre_lista = Yii::t('gestion', 'Informes de Doctorado');
        break;
    case 'iced':
        $nombre_lista = Yii::t('gestion', 'Informe de la Calidad de los Estudios de Doctorado');
        break;
    default:
        throw new NotFoundHttpException(sprintf(
            Yii::t('cati', 'No se ha encontrado el tipo de estudio «%s».  ☹'),
            $tipo
        ));
}
$this->title = sprintf(Yii::t('cati', 'Edición de un apartado del informe %d/%d'), $anyo, $anyo + 1);

$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => $nombre_lista . ' ' . $anyo . '/' . ($anyo + 1),
    'url' => ['gestion/lista-informes', 'anyo' => $anyo, 'tipo' => $pregunta->tipo],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('models', 'Apartados'),
    'url' => ['informe-pregunta/lista', 'anyo' => $anyo, 'tipo' => $pregunta->tipo],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Editar');

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php echo $this->render('_formulario', [
    'pregunta' => $pregunta,
]); ?>
