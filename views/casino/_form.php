<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Casino $model */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="casino-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <?= $form->field($model, 'name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'e.g. Royal Vegas Club',
                    ]) ?>
                </div>

                <div class="col-md-6 mb-3">
                    <?= $form->field($model, 'slug')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Leave empty to auto-generate from name',
                    ])->hint('URL-friendly unique identifier. Automatically generated from name if left blank.') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <?= $form->field($model, 'rating')->input('number', [
                        'step' => '0.1',
                        'min' => '0.0',
                        'max' => '5.0',
                        'placeholder' => '4.8',
                    ])->hint('Rating between 0.0 and 5.0') ?>
                </div>

                <div class="col-md-6 mb-3 d-flex align-items-center pt-3">
                    <?= $form->field($model, 'is_active')->checkbox([
                        'label' => 'Active Status (visible on public listing)',
                    ]) ?>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                <?= Html::submitButton($model->isNewRecord ? 'Create Casino' : 'Update Casino', ['class' => 'btn btn-primary px-4']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
