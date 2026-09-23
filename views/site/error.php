<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\bootstrap5\Html;

$this->title = $name;
?>
<div class="site-error py-5 text-center">
    <div class="card shadow-sm border-0 d-inline-block p-5" style="max-width: 600px;">
        <h1 class="display-4 text-danger mb-3"><?= Html::encode($this->title) ?></h1>
        <div class="alert alert-danger">
            <?= nl2br(Html::encode($message)) ?>
        </div>
        <p class="text-muted mt-3">
            The above error occurred while the Web server was processing your request.
        </p>
    </div>
</div>
