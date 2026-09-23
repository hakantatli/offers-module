<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Casino $model */

$this->title = 'Update Casino: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Casinos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="casino-update">
    <div class="mb-4">
        <h1 class="h2 fw-bold"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted">Modify casino details, rating, or active status.</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
