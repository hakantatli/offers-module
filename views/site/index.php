<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;

$this->title = 'Environment Ready';
?>
<div class="site-index py-5">
    <div class="p-5 mb-4 bg-light rounded-3 shadow-sm text-center">
        <h1 class="display-5 fw-bold text-success">🚀 Docker & Yii2 Setup Ready</h1>
        <p class="lead text-muted mt-3">
            Running on <strong>PHP <?= PHP_VERSION ?></strong> with Apache, MySQL 8.0, and Bootstrap 5.
        </p>
        <hr class="my-4">
        <div class="d-inline-flex gap-2 text-start">
            <ul class="list-unstyled mb-0">
                <li>✅ <strong>PHP Version:</strong> <?= PHP_VERSION ?></li>
                <li>✅ <strong>PDO MySQL:</strong> <?= extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled' ?></li>
                <li>✅ <strong>Intl:</strong> <?= extension_loaded('intl') ? 'Enabled' : 'Disabled' ?></li>
                <li>✅ <strong>Zip:</strong> <?= extension_loaded('zip') ? 'Enabled' : 'Disabled' ?></li>
                <li>✅ <strong>OPcache:</strong> <?= extension_loaded('Zend OPcache') ? 'Enabled' : 'Disabled' ?></li>
            </ul>
        </div>
    </div>
</div>
