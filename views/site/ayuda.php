<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = Yii::t('app', 'Ayuda');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <h1><?php echo Html::encode($this->title); ?></h1>
    <hr><br>

    <h2>Soporte administrativo</h2>

    <p>Según el caso, contacte con:</p>

    <ul class='listado'>
        <li>Área de Calidad y Mejora &lt;uzcalidad@unizar.es&gt;</li>
        <li>Sección de Grado y Máster &lt;grado.master@unizar.es&gt;</li>
        <li>Escuela de Doctorado &lt;docto@unizar.es&gt;</li>
    </ul>


    <h2>Soporte técnico</h2>

    <p>Abra un <em>ticket</em> en el <?php echo Html::a('Centro de Atención a Usuari@s', 'https://cau.unizar.es/'); ?>.</p>


    <h3>Problemas al iniciar sesión</h3>

    <p>
        Si tras pulsar el botón «Iniciar sesión» le vuelve a aparecer el formulario, posiblemente su navegador
        tenga un problema con las <em>cookies</em> de sesión.
    </p>

    <p>Para solucionarlo, puede probar a usar otro ordenador (u otro navegador en su PC),
       usar el modo incógnito, o borrar las <em>cookies</em>.</p>


    <h4>Modo incógnito</h4>


    <h5>Mozilla Firefox</h5>

    <p>
        Seleccione la opción de menú Archivo -> Nueva ventana privada (o pulse la combinación de teclas Ctrl +
        Mayús. + P). Vuelva a intentar iniciar sesión en la nueva ventana.
    </p>


    <h5>Google Chrome</h5>

    <p>
        En el menú desplegable seleccione Nueva ventana de incógnito (o pulse la combinación de teclas Ctrl +
        Mayús + N). Vuelva a intentar iniciar sesión en la nueva ventana.
    </p>


    <h4>Borrar cookies</h4>

    <p>Otra opción es borrar la cookie de sesión de este sitio en el navegador.</p>


    <h5>Mozilla Firefox</h5>

    <ol>
        <li>En la barra de dirección del navegador, a la izquierda, verá el icono de un candado. Pulse en él.</li>
        <li>En el menú desplegable que aparece, seleccione «Limpiar cookies y datos del sitio...».</li>
        <li>Seleccione «estudios.unizar.es» y pulse el botón «Eliminar». Repita con «unizar.es».</li>
        <li>Vuelva a intentar iniciar sesión.</li>
    </ol>


    <h5>Google Chrome</h5>

    <ol>
        <li>En la barra de dirección del navegador, a la izquierda, verá el icono de un candado. Pulse en él.</li>
        <li>En el menú desplegable que aparece, seleccione «Cookies».</li>
        <li>Seleccione «estudios.unizar.es» y pulse el botón «Quitar». Repita con «unizar.es».</li>
        <li>Pulse el botón «Aceptar» y vuelva a intentar iniciar sesión.</li>
    </ol>
</div>
