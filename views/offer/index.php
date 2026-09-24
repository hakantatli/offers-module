<?php

use app\models\Offer;
use yii\bootstrap5\Html;
use yii\bootstrap5\LinkPager;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Offer[] $offers */
/** @var yii\data\Pagination $pagination */
/** @var string|null $selectedType */
/** @var string|null $selectedCasino */
/** @var array $casinos */
/** @var array $types */

$this->title = 'Exclusive Casino Offers & Bonuses';
?>
<div class="offer-public-index">
    <!-- Hero Banner -->
    <div class="p-4 p-md-5 mb-4 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
        <div class="col-md-9 px-0">
            <h1 class="display-5 fw-bold">Casino Offers</h1>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="get" action="<?= Url::to(['offer/index']) ?>" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold text-muted small text-uppercase mb-1">Filter by Casino</label>
                    <select name="casino_id" class="form-select">
                        <option value="">-- All Casinos --</option>
                        <?php foreach ($casinos as $id => $name): ?>
                            <option value="<?= $id ?>" <?= (string)$selectedCasino === (string)$id ? 'selected' : '' ?>>
                                <?= Html::encode($name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small text-uppercase mb-1">Filter by Offer Type</label>
                    <select name="type" class="form-select">
                        <option value="">-- All Types --</option>
                        <?php foreach ($types as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $selectedType === $key ? 'selected' : '' ?>>
                                <?= Html::encode($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        Filter Offers
                    </button>
                    <?php if ($selectedCasino || $selectedType): ?>
                        <a href="<?= Url::to(['offer/index']) ?>" class="btn btn-outline-secondary" title="Clear Filters">
                            ✕
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Offers Count Bar -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-muted small">
            Showing <strong><?= count($offers) ?></strong> of <strong><?= $pagination->totalCount ?></strong> available offers
            <?php if ($selectedCasino || $selectedType): ?>
                (filtered)
            <?php endif; ?>
        </div>
        <div class="small text-muted">
            Page <strong><?= $pagination->getPage() + 1 ?></strong> of <strong><?= $pagination->getPageCount() ?></strong>
        </div>
    </div>

    <!-- Offers Cards Grid -->
    <?php if (empty($offers)): ?>
        <div class="alert alert-info py-5 text-center shadow-sm border-0">
            <h4 class="fw-bold mb-2">No Offers Found</h4>
            <p class="text-muted mb-3">No active offers match your chosen filter criteria.</p>
            <a href="<?= Url::to(['offer/index']) ?>" class="btn btn-outline-primary btn-sm">Clear All Filters</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($offers as $offer): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 d-flex flex-column">
                        <!-- Casino Header -->
                        <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark fs-6">
                                🎰 <?= Html::encode($offer->casino->name) ?>
                            </span>
                            <span class="badge bg-light text-dark border">
                                <span class="rating-stars text-warning">★</span> <?= number_format($offer->casino->rating, 2) ?>
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body d-flex flex-column pt-2">
                            <div class="mb-2">
                                <span class="badge <?= $offer->getTypeBadgeClass() ?>">
                                    <?= Html::encode(Offer::getTypes()[$offer->type] ?? $offer->type) ?>
                                </span>
                            </div>

                            <h5 class="card-title fw-bold mb-2">
                                <?= Html::a(
                                    Html::encode($offer->title),
                                    ['offer/view', 'slug' => $offer->slug],
                                    ['class' => 'text-dark text-decoration-none']
                                ) ?>
                            </h5>

                            <div class="my-2">
                                <span class="display-6 fw-bold text-success">
                                    €<?= number_format($offer->amount, 2) ?>
                                </span>
                            </div>

                            <p class="card-text text-muted small mb-3 flex-grow-1">
                                <?= Html::encode(mb_strimwidth($offer->terms, 0, 110, '...')) ?>
                            </p>

                            <div class="border-top pt-2 mt-auto d-flex justify-content-between align-items-center small text-muted">
                                <span>
                                    <?php if ($offer->expires_at): ?>
                                        ⏳ Expires <?= Yii::$app->formatter->asDate($offer->expires_at, 'php:M j, Y') ?>
                                    <?php else: ?>
                                        ♾️ Ongoing offer
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer bg-white border-0 pb-3 pt-0">
                            <?= Html::a('View Details & Terms &rarr;', ['offer/view', 'slug' => $offer->slug], [
                                'class' => 'btn btn-primary w-100 fw-semibold',
                            ]) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            <?= LinkPager::widget([
                'pagination' => $pagination,
                'options' => ['class' => 'pagination shadow-sm'],
                'linkContainerOptions' => ['class' => 'page-item'],
                'linkOptions' => ['class' => 'page-link'],
                'disabledListItemSubTagOptions' => ['tag' => 'span', 'class' => 'page-link text-muted'],
            ]) ?>
        </div>
    <?php endif; ?>
</div>
