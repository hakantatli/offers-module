<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Offer $model */
/** @var array $casinos */

$this->title = 'Create Offer';
$this->params['breadcrumbs'][] = ['label' => 'Offers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-create">
    <div class="mb-4">
        <h1 class="h2 fw-bold"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted">Create a new casino bonus or promotional offer.</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'casinos' => $casinos,
    ]) ?>
</div>
