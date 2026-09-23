<?php

use app\models\Offer;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Offer $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Offers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-view">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">Offer terms, bonus value, and validity period.</p>
        </div>
        <div>
            <?= Html::a('&larr; Back to List', ['index'], ['class' => 'btn btn-outline-secondary me-2']) ?>
            <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary me-2']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => "Are you sure you want to delete offer '{$model->title}'?",
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
                    'title',
                    'slug',
                    [
                        'attribute' => 'casino_id',
                        'label' => 'Casino',
                        'format' => 'raw',
                        'value' => $model->casino
                            ? Html::a(Html::encode($model->casino->name), ['casino/view', 'id' => $model->casino->id])
                            : '<span class="text-muted">N/A</span>',
                    ],
                    [
                        'attribute' => 'type',
                        'format' => 'raw',
                        'value' => '<span class="badge ' . $model->getTypeBadgeClass() . '">'
                            . Html::encode(Offer::getTypes()[$model->type] ?? $model->type)
                            . '</span>',
                    ],
                    [
                        'attribute' => 'amount',
                        'format' => 'raw',
                        'value' => '<span class="h5 fw-bold text-success">€' . number_format($model->amount, 2) . '</span>',
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => '<span class="badge ' . $model->getStatusBadgeClass() . '">'
                            . Html::encode(Offer::getStatuses()[$model->status] ?? $model->status)
                            . '</span>',
                    ],
                    [
                        'attribute' => 'expires_at',
                        'format' => 'raw',
                        'value' => function (Offer $model) {
                            if (!$model->expires_at) {
                                return '<span class="text-muted">No Expiration Date</span>';
                            }
                            $isExpired = strtotime($model->expires_at) < time();
                            $dateStr = Yii::$app->formatter->asDatetime($model->expires_at, 'php:Y-m-d H:i:s');
                            if ($isExpired) {
                                return '<span class="text-danger fw-bold">Expired on ' . $dateStr . '</span>';
                            }
                            return '<span class="text-success fw-bold">Active until ' . $dateStr . '</span>';
                        },
                    ],
                    [
                        'attribute' => 'terms',
                        'format' => 'ntext',
                    ],
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>
    </div>
</div>
