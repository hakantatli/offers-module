<?php

use app\models\Offer;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Offer $model */

$this->title = $model->title . ' - ' . $model->casino->name;
$this->params['breadcrumbs'][] = ['label' => 'Offers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
?>
<div class="offer-public-view">
    <div class="mb-3">
        <?= Breadcrumbs::widget([
            'links' => $this->params['breadcrumbs'] ?? [],
            'options' => ['class' => 'breadcrumb small bg-transparent p-0 mb-3'],
        ]) ?>
    </div>

    <div class="row g-4">
        <!-- Main Details Column -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                        <span class="badge <?= $model->getTypeBadgeClass() ?> px-3 py-2 fs-6">
                            <?= Html::encode(Offer::getTypes()[$model->type] ?? $model->type) ?>
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2 fs-6">
                            🎰 <?= Html::encode($model->casino->name) ?>
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2 fs-6">
                            <span class="rating-stars text-warning">★</span> <?= number_format($model->casino->rating, 2) ?> / 5.0
                        </span>
                    </div>

                    <h1 class="h2 fw-bold text-dark mb-4"><?= Html::encode($model->title) ?></h1>

                    <!-- Highlight Value Box -->
                    <div class="p-4 rounded-3 bg-light border text-center mb-4">
                        <small class="text-uppercase fw-semibold text-muted tracking-wide d-block mb-1">Total Bonus Value</small>
                        <div class="display-4 fw-bold text-success mb-2">€<?= number_format($model->amount, 2) ?></div>
                        <div>
                            <?php if ($model->expires_at): ?>
                                <span class="badge bg-warning text-dark px-3 py-1">
                                    ⏳ Valid until <?= Yii::$app->formatter->asDatetime($model->expires_at, 'php:F j, Y - H:i') ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success px-3 py-1">♾️ Ongoing Promotional Offer</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mb-4">
                        <h4 class="fw-bold mb-3">Terms & Wagering Requirements</h4>
                        <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                            <p class="mb-0 text-secondary" style="white-space: pre-line; line-height: 1.6;">
                                <?= Html::encode($model->terms) ?>
                            </p>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 mt-4 pt-3 border-top">
                        <a href="#claim" class="btn btn-primary btn-lg px-5 fw-bold shadow-sm" onclick="alert('Demo: Redirecting to official casino registration...'); return false;">
                            Claim This Bonus Now &rarr;
                        </a>
                        <?= Html::a('&larr; Back to All Offers', ['offer/index'], ['class' => 'btn btn-outline-secondary btn-lg px-4']) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <!-- Casino Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Casino Information</h5>
                </div>
                <div class="card-body">
                    <h4 class="fw-bold text-primary mb-2"><?= Html::encode($model->casino->name) ?></h4>
                    <div class="mb-3">
                        <span class="rating-stars text-warning fs-5">★</span>
                        <strong class="fs-5"><?= number_format($model->casino->rating, 2) ?></strong>
                        <span class="text-muted">/ 5.0 Rating</span>
                    </div>

                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Promotions:</span>
                            <?= Html::a('View all ' . Html::encode($model->casino->name) . ' offers', ['offer/index', 'casino_id' => $model->casino->id], ['class' => 'text-decoration-none']) ?>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
