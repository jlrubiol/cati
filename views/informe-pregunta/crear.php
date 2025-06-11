<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

switch ($tipo) {
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
$this->title = Yii::t('cati', 'Nuevo apartado');

$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => sprintf('%s %d/%d', $nombre_lista, $anyo, $anyo + 1),
    'url' => ['gestion/lista-informes', 'anyo' => $anyo, 'tipo' => $tipo],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('models', 'Apartados'),
    'url' => ['informe-pregunta/lista', 'anyo' => $anyo, 'tipo' => $tipo],
];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php echo $this->render(
    '_formulario',
    [
        'pregunta' => $pregunta,
        'tipo' => $tipo,
    ]
); ?>
