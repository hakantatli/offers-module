<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Casino $model */

$this->title = 'Create Casino';
$this->params['breadcrumbs'][] = ['label' => 'Casinos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="casino-create">
    <div class="mb-4">
        <h1 class="h2 fw-bold"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted">Add a new casino operator to the platform.</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
