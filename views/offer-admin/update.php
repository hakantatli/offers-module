<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Offer $model */
/** @var array $casinos */

$this->title = 'Update Offer: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Offers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="offer-update">
    <div class="mb-4">
        <h1 class="h2 fw-bold"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted">Modify promotional offer conditions, status or expiration.</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'casinos' => $casinos,
    ]) ?>
</div>
