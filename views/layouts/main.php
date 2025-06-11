<?php
/**
 * Vista de la plantilla principal de las páginas web.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */

/* @var $this \yii\web\View */
/* @var $content string */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\Enlace;
use app\models\User;
use app\widgets\Alert;

$bundle = AppAsset::register($this);
$webuser = Yii::$app->user;  // yii\web\User
$user = $webuser->identity;  // app\models\User

$this->registerMetaTag([
    'name' => 'author',
    'content' => Yii::t(
        'app',
        'Área de Aplicaciones. Servicio de Informática y Comunicaciones de la Universidad de Zaragoza.'
    ),
]);

$e = new Enlace();
$enlaces = $e->getUrls();

$anyo_academico = date('m') < 10 ? date('Y') - 2 : date('Y') - 1;
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?php echo Html::encode(Yii::$app->language); ?>">
<head>
    <meta charset="<?php echo Yii::$app->charset; ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo Html::csrfMetaTags(); ?>
    <title><?php echo Html::encode($this->title); ?></title>
    <link rel="icon" type="image/x-icon" href="<?php echo Url::home(); ?>favicon.ico">

    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.dataTables.min.css"/>

    <?php $this->head(); ?>
    <!-- Matomo -->
    <script type="text/javascript">
        // var _paq = window._paq = window._paq || [];
        // /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
        // _paq.push(['trackPageView']);
        // _paq.push(['enableLinkTracking']);
        // (function() {
        //     var u = "//staweb.unizar.es/matomo/";
        //     _paq.push(['setTrackerUrl', u + 'matomo.php']);
        //     _paq.push(['setSiteId', '138']);
        //     var d = document,
        //         g = d.createElement('script'),
        //         s = d.getElementsByTagName('script')[0];
        //     g.async = true;
        //     g.src = u + 'matomo.js';
        //     s.parentNode.insertBefore(g, s);
        // })();
    </script>
    <noscript><p><!-- img src="//webstats.unizar.es/matomo.php?idsite=138&amp;rec=1" style="border: 0;" alt="" / --></p></noscript>
    <!-- End Matomo Code -->
</head>

<body>
<?php $this->beginBody(); ?>

<div class="wrap">
    <?php
    if (Url::current() == Url::toRoute('site/index')) {
        NavBar::begin([
            'brandLabel' => '<span class="icon-logoUZ"></span>'
              . ' <span class="screen-reader">Universidad de Zaragoza</span>',
            'brandUrl' => 'http://www.unizar.es',
            'options' => ['class' => 'navbar-inverse navbar-fixed-top'],
        ]);
    } else {
        NavBar::begin([
            'options' => ['class' => 'navbar-inverse navbar-fixed-top'],
        ]);
    }

    echo "\n" . Nav::widget([
        'activateItems' => false,
        'encodeLabels' => false,
        'items' => [
            [
                'label' => '<i class="glyphicon glyphicon-globe navbar-icono"></i> &nbsp;'
                        . ((Yii::$app->language === 'es') ? 'English' : 'Castellano'),
                'url' => ['//language/set'],
                'linkOptions' => [
                    'data-method' => 'post',
                    'data-params' => ['language' => Yii::$app->language === 'es' ? 'en' : 'es'],
                ],
            ], [
                'label' => '<i class="glyphicon glyphicon-link navbar-icono"></i> &nbsp;'
                        . Yii::t('cati', 'Enlaces'),
                'items' => $enlaces,
                'visible' => !empty($enlaces),
            ], [
                'label' => '<i class="glyphicon glyphicon-cog navbar-icono"></i> &nbsp;'
                        . Yii::t('app', 'Gestión'),
                'items' => [
                    [
                        'label' => Yii::t('cati', 'Mis estudios'),
                        'url' => ['//gestion/mis-estudios'],
                    ],
                    ($webuser->can('gestor')) ? '<li role="presentation" class="divider"></li>' : '',
                    [
                        'label' => Yii::t('cati', 'Unidad de Calidad'),
                        'url' => ['//gestion/calidad'],
                        'visible' => $webuser->can('unidadCalidad'),
                    ], [
                        'label' => Yii::t('cati', 'Grado y Máster'),
                        'url' => ['//gestion/grado-master'],
                        'visible' => $webuser->can('gradoMaster'),
                    ], [
                        'label' => Yii::t('cati', 'Doctorado'),
                        'url' => ['//gestion/doctorado'],
                        'visible' => $webuser->can('escuelaDoctorado'),
                    ], [
                        'label' => Yii::t('cati', 'Comisión de Doctorado'),
                        'url' => ['//gestion/comision-doctorado'],
                        'visible' => ($webuser->identity && $webuser->identity->esComisionDoctorado()),
                    ],
                ],
                'visible' => !Yii::$app->user->isGuest,
            ], [
                'label' => '<i class="glyphicon glyphicon-wrench navbar-icono"></i> &nbsp;' . Yii::t('cati', 'Administración'),
                'url' => ['//user/admin'],
                'visible' => $user && $user->isAdmin,
            ], [
                'encode' => false,
                'label' => '<i class="glyphicon glyphicon-question-sign navbar-icono"></i> &nbsp;' .
                            Yii::t('app', 'Ayuda'),
                'url' => ['//site/ayuda'],
            ],
            $webuser->isGuest ? [
                'label' => '<i class="glyphicon glyphicon-log-in navbar-icono"></i> &nbsp;' .
                            Yii::t('app', 'Iniciar sesión'),
                'url' => ['//saml/login'],  // Yii::$app->user->loginUrl,
            ] : [
                'label' => '<i class="glyphicon glyphicon-log-out navbar-icono"></i> &nbsp;' .
                            sprintf('%s (%s)', Yii::t('app', 'Cerrar sesión'), $user->username),
                'url' => empty(Yii::$app->session->get('real_identity_id')) ? ['//saml/logout'] : ['//suplantacion/logout'],
                // 'linkOptions' => ['data-method' => 'post'],
            ],
        ],
        'options' => ['class' => 'navbar-nav navbar-right'],
    ]);

    NavBar::end();
    ?>


    <header class="container" id="banner">
        <div class="region">
            <div id="block-marcadelsitio">
                <a href="http://www.unizar.es" title="Universidad de Zaragoza" class="site-logo">
                    <img src="<?php echo $bundle->baseUrl . '/css/img/logo.svg'; ?>"
                      alt="Universidad de Zaragoza" style="width: 220px; height: auto;" />
                </a>
            </div>
            <div id="block-smallsitetitleblock" class='noprint'>
                <a href="<?php echo Yii::$app->homeUrl; ?>" title="Inicio" rel="home">
                <div class="rotulo-cabecera">
                    <?php echo Yii::t(
                        'cati',
                        'Oferta de <strong>estudios</strong><br> <strong>oficiales</strong> universitarios'
                    ); ?>
                </div>
                </a>
            </div>
        </div>
    </header>


    <div class="container" id="contenedor-principal">
        <?php
        echo Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]);
        echo Alert::widget();
        echo $content;
        echo '<hr class="hideinmainpage">';
        echo Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]);
        ?>
    </div>
</div>

<footer class="footer container">
    <div class="row">
        <div class="col-lg-8">
            <?php
            printf("&copy; %s %s<br>\n", date('Y'), Yii::t('app', 'Universidad de Zaragoza'));
            printf(
                "&copy; %d %s (%s)\n",
                date('Y'),
                Yii::t('app', 'Servicio de Informática y Comunicaciones de la Universidad de Zaragoza'),
                Html::a('SICUZ', 'http://sicuz.unizar.es')
            );
            ?>
        </div>

        <div class="col-lg-2" style="text-align: right;">
            Universidad de Zaragoza<br>
            C/ Pedro Cerbuna, 12<br>
            ES-50009 Zaragoza<br>
            España / Spain<br>
            Tel: +34 976761000<br>
            ciu@unizar.es<br>
            Q-5018001-G<br>
            <br>
            <a href="https://www.facebook.com/unizar.es">
                <span class="icon-facebook"></span><span class="screen-reader">Facebook</span>
            </a> &nbsp;
            <a href="https://twitter.com/unizar">
                <span class="icon-twitter"></span><span class="screen-reader">Twitter</span>
            </a>
        </div>

        <div class="col-lg-2">
            <a href="http://www.unizar.es">
                <span class="icon-unizar_es"></span><span class="screen-reader">Universidad de Zaragoza</span>
            </a>
        </div>
    </div>
    <hr style="border-color: #3b3b3b;">

    <p class="pull-right" style="font-size: 1.2rem">
        <a href="http://www.unizar.es/aviso-legal" target="_blank">
            <?php echo Yii::t('app', 'Aviso legal'); ?>
        </a> &nbsp; | &nbsp;
        <a href="http://www.unizar.es/condiciones-generales-de-uso" target="_blank">
            <?php echo Yii::t('app', 'Condiciones generales de uso'); ?>
        </a> &nbsp; | &nbsp;
        <a href="http://www.unizar.es/politica-de-privacidad" target="_blank">
            <?php echo Yii::t('app', 'Política de privacidad'); ?>
        </a>
    </p>
</footer>

<?php $this->endBody(); ?>

<script>
    // Javascript to enable link to tab
    // Ver <https://stackoverflow.com/questions/7862233/twitter-bootstrap-tabs-go-to-specific-tab-on-page-reload-or-hyperlink>
    // Ver <https://stackoverflow.com/questions/19163188/bootstrap-tabs-opening-tabs-on-another-page>
    $(document).ready(function() {
        var hash = window.location.hash;
        if (hash)
            $('ul.nav a[href="' + hash + '"]').tab('show');

        // Change hash for page-reload
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash
            // window.scrollTo(0, 0);
        });
        /*
        $('.nav-tabs a').click(function (e) {
            $(this).tab('show');
            var scrollmem = $('body').scrollTop() || $('html').scrollTop();
            window.location.hash = this.hash;
            $('html,body').scrollTop(scrollmem);
        });
        */
    });
</script>

</body>
</html>
<?php $this->endPage(); ?>
