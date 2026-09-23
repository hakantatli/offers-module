<?php

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Casino $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Casinos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="casino-view">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">Casino profile details and status.</p>
        </div>
        <div>
            <?= Html::a('&larr; Back to List', ['index'], ['class' => 'btn btn-outline-secondary me-2']) ?>
            <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary me-2']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => "Are you sure you want to delete '{$model->name}'?",
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    'slug',
                    [
                        'attribute' => 'rating',
                        'format' => 'raw',
                        'value' => '<span class="rating-stars text-warning">★</span> ' . number_format($model->rating, 2) . ' / 5.00',
                    ],
                    [
                        'attribute' => 'is_active',
                        'format' => 'raw',
                        'value' => $model->is_active
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-secondary">Inactive</span>',
                    ],
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>
    </div>
</div>
