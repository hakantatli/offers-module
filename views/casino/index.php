<?php

use app\models\Casino;
use yii\bootstrap5\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\CasinoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Manage Casinos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="casino-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 fw-bold"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">Create, update, filter and manage casino operators.</p>
        </div>
        <div>
            <?= Html::a('+ New Casino', ['create'], ['class' => 'btn btn-primary shadow-sm']) ?>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover table-striped align-middle mb-0'],
                    'pager' => [
                        'class' => 'yii\bootstrap5\LinkPager',
                        'options' => ['class' => 'pagination justify-content-center p-3 mb-0'],
                    ],
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'headerOptions' => ['style' => 'width: 70px;'],
                        ],
                        [
                            'attribute' => 'name',
                            'format' => 'raw',
                            'value' => function (Casino $model) {
                                return Html::a(Html::encode($model->name), ['view', 'id' => $model->id], [
                                    'class' => 'fw-semibold text-decoration-none',
                                ]);
                            },
                        ],
                        'slug',
                        [
                            'attribute' => 'rating',
                            'format' => 'raw',
                            'filterInputOptions' => [
                                'class' => 'form-control',
                                'placeholder' => 'e.g. >4.5',
                            ],
                            'headerOptions' => ['style' => 'width: 140px;'],
                            'value' => function (Casino $model) {
                                return '<span class="rating-stars text-warning">★</span> ' . number_format($model->rating, 2);
                            },
                        ],
                        [
                            'attribute' => 'is_active',
                            'format' => 'raw',
                            'filter' => [1 => 'Active', 0 => 'Inactive'],
                            'filterInputOptions' => [
                                'class' => 'form-select',
                                'prompt' => 'All',
                            ],
                            'headerOptions' => ['style' => 'width: 140px;'],
                            'value' => function (Casino $model) {
                                if ($model->is_active) {
                                    return '<span class="badge bg-success">Active</span>';
                                }
                                return '<span class="badge bg-secondary">Inactive</span>';
                            },
                        ],
                        [
                            'attribute' => 'created_at',
                            'format' => ['datetime', 'php:Y-m-d H:i'],
                            'headerOptions' => ['style' => 'width: 170px;'],
                        ],
                        [
                            'class' => ActionColumn::class,
                            'header' => 'Actions',
                            'headerOptions' => ['style' => 'width: 130px; text-align: right;'],
                            'contentOptions' => ['style' => 'text-align: right;'],
                            'buttons' => [
                                'view' => function ($url) {
                                    return Html::a('View', $url, ['class' => 'btn btn-sm btn-outline-info me-1']);
                                },
                                'update' => function ($url) {
                                    return Html::a('Edit', $url, ['class' => 'btn btn-sm btn-outline-primary me-1']);
                                },
                                'delete' => function ($url, $model) {
                                    return Html::a('Delete', $url, [
                                        'class' => 'btn btn-sm btn-outline-danger',
                                        'data' => [
                                            'confirm' => "Are you sure you want to delete '{$model->name}'?",
                                            'method' => 'post',
                                        ],
                                    ]);
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
