<?php

use app\models\Offer;
use yii\bootstrap5\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\OfferSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $casinos */

$this->title = 'Manage Offers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-admin-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 fw-bold"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">Search, filter, sort and manage all casino promotional offers.</p>
        </div>
        <div>
            <?= Html::a('+ New Offer', ['create'], ['class' => 'btn btn-primary shadow-sm']) ?>
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
                            'headerOptions' => ['style' => 'width: 60px;'],
                        ],
                        [
                            'attribute' => 'title',
                            'format' => 'raw',
                            'value' => function (Offer $model) {
                                return '<div>' . Html::a(Html::encode($model->title), ['view', 'id' => $model->id], [
                                    'class' => 'fw-semibold text-decoration-none',
                                ]) . '</div><small class="text-muted">' . Html::encode($model->slug) . '</small>';
                            },
                        ],
                        [
                            'attribute' => 'casino_id',
                            'label' => 'Casino',
                            'format' => 'raw',
                            'filter' => $casinos,
                            'filterInputOptions' => [
                                'class' => 'form-select',
                                'prompt' => 'All Casinos',
                            ],
                            'headerOptions' => ['style' => 'width: 170px;'],
                            'value' => function (Offer $model) {
                                if (!$model->casino) {
                                    return '<span class="text-muted">N/A</span>';
                                }
                                return Html::a(Html::encode($model->casino->name), ['casino/view', 'id' => $model->casino->id], [
                                    'class' => 'text-decoration-none',
                                ]);
                            },
                        ],
                        [
                            'attribute' => 'type',
                            'format' => 'raw',
                            'filter' => Offer::getTypes(),
                            'filterInputOptions' => [
                                'class' => 'form-select',
                                'prompt' => 'All Types',
                            ],
                            'headerOptions' => ['style' => 'width: 150px;'],
                            'value' => function (Offer $model) {
                                $label = Offer::getTypes()[$model->type] ?? $model->type;
                                return '<span class="badge ' . $model->getTypeBadgeClass() . '">' . Html::encode($label) . '</span>';
                            },
                        ],
                        [
                            'attribute' => 'amount',
                            'format' => 'raw',
                            'filterInputOptions' => [
                                'class' => 'form-control',
                                'placeholder' => 'e.g. >=100',
                            ],
                            'headerOptions' => ['style' => 'width: 120px; text-align: right;'],
                            'contentOptions' => ['style' => 'text-align: right;'],
                            'value' => function (Offer $model) {
                                return '<span class="fw-bold text-success">€' . number_format($model->amount, 2) . '</span>';
                            },
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'filter' => Offer::getStatuses(),
                            'filterInputOptions' => [
                                'class' => 'form-select',
                                'prompt' => 'All Statuses',
                            ],
                            'headerOptions' => ['style' => 'width: 130px;'],
                            'value' => function (Offer $model) {
                                $label = Offer::getStatuses()[$model->status] ?? $model->status;
                                return '<span class="badge ' . $model->getStatusBadgeClass() . '">' . Html::encode($label) . '</span>';
                            },
                        ],
                        [
                            'attribute' => 'expires_at',
                            'format' => 'raw',
                            'headerOptions' => ['style' => 'width: 170px;'],
                            'value' => function (Offer $model) {
                                if (!$model->expires_at) {
                                    return '<span class="text-muted small">No expiration</span>';
                                }
                                $isExpired = strtotime($model->expires_at) < time();
                                $formattedDate = Yii::$app->formatter->asDatetime($model->expires_at, 'php:Y-m-d H:i');

                                if ($isExpired) {
                                    return '<span class="text-danger small fw-semibold">⚠️ ' . $formattedDate . '</span>';
                                }
                                return '<span class="text-dark small">' . $formattedDate . '</span>';
                            },
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
                                            'confirm' => "Are you sure you want to delete offer '{$model->title}'?",
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
