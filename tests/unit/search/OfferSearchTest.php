<?php

namespace app\tests\unit\search;

use PHPUnit\Framework\TestCase;
use app\models\Offer;
use app\models\OfferSearch;

class OfferSearchTest extends TestCase
{
    public function testSearchByTitle(): void
    {
        $searchModel = new OfferSearch();
        $dataProvider = $searchModel->search(['OfferSearch' => ['title' => 'Welcome']]);
        $models = $dataProvider->getModels();

        $this->assertNotEmpty($models);
        foreach ($models as $offer) {
            $this->assertInstanceOf(Offer::class, $offer);
            $this->assertStringContainsStringIgnoringCase('Welcome', $offer->title);
        }
    }

    public function testSearchByType(): void
    {
        $searchModel = new OfferSearch();
        $dataProvider = $searchModel->search(['OfferSearch' => ['type' => Offer::TYPE_NO_DEPOSIT]]);
        $models = $dataProvider->getModels();

        $this->assertNotEmpty($models);
        foreach ($models as $offer) {
            $this->assertInstanceOf(Offer::class, $offer);
            $this->assertEquals(Offer::TYPE_NO_DEPOSIT, $offer->type);
        }
    }

    public function testSearchByStatus(): void
    {
        $searchModel = new OfferSearch();
        $dataProvider = $searchModel->search(['OfferSearch' => ['status' => Offer::STATUS_ACTIVE]]);
        $models = $dataProvider->getModels();

        $this->assertNotEmpty($models);
        foreach ($models as $offer) {
            $this->assertInstanceOf(Offer::class, $offer);
            $this->assertEquals(Offer::STATUS_ACTIVE, $offer->status);
        }
    }

    public function testSearchByAmountOperators(): void
    {
        $searchModel = new OfferSearch();

        // Amount >= 100
        $dataProvider = $searchModel->search(['OfferSearch' => ['amount' => '>=100']]);
        $models = $dataProvider->getModels();
        $this->assertNotEmpty($models);
        foreach ($models as $offer) {
            $this->assertInstanceOf(Offer::class, $offer);
            $this->assertGreaterThanOrEqual(100.0, (float)$offer->amount);
        }

        // Amount <= 50
        $dataProvider = $searchModel->search(['OfferSearch' => ['amount' => '<=50']]);
        $models = $dataProvider->getModels();
        $this->assertNotEmpty($models);
        foreach ($models as $offer) {
            $this->assertInstanceOf(Offer::class, $offer);
            $this->assertLessThanOrEqual(50.0, (float)$offer->amount);
        }
    }

    public function testSearchByCasinoNameRelation(): void
    {
        $searchModel = new OfferSearch();
        $dataProvider = $searchModel->search(['OfferSearch' => ['casino_name' => 'BitStarz']]);
        $models = $dataProvider->getModels();

        $this->assertNotEmpty($models);
        foreach ($models as $offer) {
            $this->assertInstanceOf(Offer::class, $offer);
            $this->assertStringContainsStringIgnoringCase('BitStarz', $offer->casino->name);
        }
    }
}
