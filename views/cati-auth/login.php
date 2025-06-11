<?php
/**
 * @var yii\web\View
 * @var $model       webvimark\modules\UserManagement\models\forms\LoginForm
 */
use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = UserManagementModule::t('front', 'Log in');
$this->registerMetaTag([
    'name' => 'description',
    'content' => Yii::t(
        'cati',
        'Formulario para ingresar en la web de estudios de la Universidad de Zaragoza'
    ),
]);
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="container" id="login-wrapper">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title"><?= UserManagementModule::t('front', 'Authorization') ?></h1>
                </div>

                <div class="panel-body">

                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'options' => ['autocomplete' => 'off'],
                        'validateOnBlur' => false,
                        'fieldConfig' => [
                            'template' => "{input}\n{error}",
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'username')->textInput([
                            // 'placeholder'  => $model->getAttributeLabel('username'),
                            'placeholder' => Yii::t('cati', 'Usuario de correo electrónico'),
                            'autocomplete' => 'on',
                            'title' => Yii::t('cati', 'Usuario de correo electrónico (lo que va antes de @unizar.es)'),
                        ]) ?>

                    <?= $form->field($model, 'password')->passwordInput([
                            'placeholder' => $model->getAttributeLabel('password'),
                            'autocomplete' => 'off',
                            'title' => Yii::t('cati', 'Introduzca la contraseña del correo'),
                        ]) ?>

                    <?= (isset(Yii::$app->user->enableAutoLogin) && Yii::$app->user->enableAutoLogin) ? $form->field($model, 'rememberMe')->checkbox(['value' => true]) : '' ?>

                    <?= Html::submitButton(
                        UserManagementModule::t('front', 'Log in'),
                        ['class' => 'btn btn-lg btn-primary btn-block']
                    ) ?>

                    <div class="row registration-block">
                        <div class="col-sm-6">
                            <?= GhostHtml::a(
                                UserManagementModule::t('front', 'Registration'),
                                ['/user-management/auth/registration']
                            ) ?>
                        </div>
                        <div class="col-sm-6 text-right">
                            <?= GhostHtml::a(
                                UserManagementModule::t('front', 'Forgot password ?'),
                                ['/user-management/auth/password-recovery']
                            ) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$css = <<<CSS
html, body {
    /* background: #eee; */
    background: url("/css/img/bg-pattern_nuevo1.png") #88A repeat scroll center 0;
    background-size: 1500px;
    -webkit-box-shadow: inset 0 0 100px rgba(0,0,0,.5);
    box-shadow: inset 0 0 100px rgba(0,0,0,.5);
    height: 100%;
    min-height: 100%;
    position: relative;
}
#login-wrapper {
    position: relative;
    top: 30%;
    margin: 3rem 0 2rem 0;
}
#login-wrapper .registration-block {
    margin-top: 15px;
}
#banner {
    display: none;
}
CSS;

$this->registerCss($css);
?>
