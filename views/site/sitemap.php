<?php

/** @var yii\web\View $this */
/** @var app\models\Casino[] $casinos */

use yii\helpers\Url;

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- ============================================================ -->
    <!-- Main Listing Pages -->
    <!-- ============================================================ -->
    <url>
        <loc><?= htmlspecialchars(Url::to(['offer/index'], true), ENT_QUOTES | ENT_XML1, 'UTF-8') ?></loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
<?php foreach ($casinos as $casino): ?>

    <!-- ============================================================ -->
    <!-- Casino: <?= htmlspecialchars($casino->name, ENT_QUOTES | ENT_XML1, 'UTF-8') ?> (Rating: <?= number_format($casino->rating, 2) ?>★) -->
    <!-- ============================================================ -->
    <url>
        <loc><?= htmlspecialchars(Url::to(['offer/casino', 'slug' => $casino->slug], true), ENT_QUOTES | ENT_XML1, 'UTF-8') ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($casino->updated_at ?: $casino->created_at)) ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
<?php if (!empty($casino->offers)): ?>
    <!-- Active Offers for <?= htmlspecialchars($casino->name, ENT_QUOTES | ENT_XML1, 'UTF-8') ?> -->
<?php foreach ($casino->offers as $offer): ?>
    <!-- Offer for <?= htmlspecialchars($casino->name, ENT_QUOTES | ENT_XML1, 'UTF-8') ?>: <?= htmlspecialchars($offer->title, ENT_QUOTES | ENT_XML1, 'UTF-8') ?> -->
    <url>
        <loc><?= htmlspecialchars(Url::to(['offer/view', 'slug' => $offer->slug], true), ENT_QUOTES | ENT_XML1, 'UTF-8') ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($offer->updated_at ?: $offer->created_at)) ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
<?php endforeach; ?>
<?php endif; ?>
<?php endforeach; ?>
</urlset>
