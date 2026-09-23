<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?> - <?= Html::encode(Yii::$app->name) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => '🎰 ' . Yii::$app->name,
        'brandUrl' => ['/offer/index'],
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark shadow-sm sticky-top']
    ]);

    $menuItems = [
        ['label' => 'Offers', 'url' => ['/offer/index']],
    ];

    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Admin Login', 'url' => ['/site/login']];
    } else {
        $menuItems[] = ['label' => '🎰 Casinos', 'url' => ['/casino/index']];
        $menuItems[] = ['label' => '🎁 Manage Offers', 'url' => ['/offer-admin/index']];
        $menuItems[] = '<li class="nav-item">'
            . Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline'])
            . Html::submitButton(
                'Logout (' . Html::encode(Yii::$app->user->identity->username) . ')',
                ['class' => 'nav-link btn btn-link text-decoration-none border-0']
            )
            . Html::endForm()
            . '</li>';
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto mb-2 mb-md-0'],
        'items' => $menuItems,
    ]);

    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container py-4">
        <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
            <div class="alert alert-<?= Html::encode($type === 'error' ? 'danger' : $type) ?> alert-dismissible fade show shadow-sm" role="alert">
                <?= Html::encode($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; ?>

        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-white border-top">
    <div class="container text-center text-muted">
        <small>&copy; <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?>. All rights reserved.</small>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
