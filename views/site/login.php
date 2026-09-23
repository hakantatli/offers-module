<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Admin Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold">🔐 Admin Login</h3>
                    <p class="text-muted small">Please enter your credentials to manage casinos.</p>
                </div>

                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                ]); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'admin']) ?>

                <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'admin123']) ?>

                <?= $form->field($model, 'rememberMe')->checkbox() ?>

                <div class="d-grid mt-4">
                    <?= Html::submitButton('Sign In', ['class' => 'btn btn-primary btn-block py-2 fw-semibold', 'name' => 'login-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>

                <div class="mt-3 p-2 bg-light rounded text-center small text-muted">
                    Default demo credentials: <code>admin</code> / <code>admin123</code>
                </div>
            </div>
        </div>
    </div>
</div>
