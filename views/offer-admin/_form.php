<?php

use app\models\Offer;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Offer $model */
/** @var array $casinos */
/** @var yii\bootstrap5\ActiveForm $form */

// Format expires_at for HTML5 datetime-local input (Y-m-d\TH:i)
$expiresAtFormatted = '';
if (!empty($model->expires_at)) {
    $expiresAtFormatted = date('Y-m-d\TH:i', strtotime($model->expires_at));
}
?>

<div class="offer-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <?= $form->field($model, 'casino_id')->dropDownList($casinos, [
                        'prompt' => '-- Select Casino Operator --',
                        'class' => 'form-select',
                    ]) ?>
                </div>

                <div class="col-md-6 mb-3">
                    <?= $form->field($model, 'title')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'e.g. 100% Match Welcome Bonus up to €500',
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <?= $form->field($model, 'slug')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Leave empty to auto-generate from title',
                    ])->hint('Unique URL identifier. Automatically generated from title if left blank.') ?>
                </div>

                <div class="col-md-6 mb-3">
                    <?= $form->field($model, 'type')->dropDownList(Offer::getTypes(), [
                        'class' => 'form-select',
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <?= $form->field($model, 'amount')->input('number', [
                        'step' => '0.01',
                        'min' => '0',
                        'placeholder' => '500.00',
                    ])->hint('Bonus value in Euros') ?>
                </div>

                <div class="col-md-4 mb-3">
                    <?= $form->field($model, 'status')->dropDownList(Offer::getStatuses(), [
                        'class' => 'form-select',
                    ]) ?>
                </div>

                <div class="col-md-4 mb-3">
                    <?= $form->field($model, 'expires_at')->input('datetime-local', [
                        'value' => $expiresAtFormatted,
                    ])->hint('Optional. Must be a date in the future if set.') ?>
                </div>
            </div>

            <div class="mb-3">
                <?= $form->field($model, 'terms')->textarea([
                    'rows' => 4,
                    'placeholder' => 'Detailed terms and conditions: Wagering requirements, min deposit, eligible games, max bet...',
                ]) ?>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                <?= Html::submitButton($model->isNewRecord ? 'Create Offer' : 'Update Offer', ['class' => 'btn btn-primary px-4']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
