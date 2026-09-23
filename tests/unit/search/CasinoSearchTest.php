<?php

namespace app\tests\unit\search;

use PHPUnit\Framework\TestCase;
use app\models\Casino;
use app\models\CasinoSearch;

class CasinoSearchTest extends TestCase
{
    public function testSearchByName()
    {
        $searchModel = new CasinoSearch();
        $dataProvider = $searchModel->search(['CasinoSearch' => ['name' => 'BitStarz']]);
        $models = $dataProvider->getModels();

        $this->assertNotEmpty($models);
        foreach ($models as $casino) {
            $this->assertStringContainsStringIgnoringCase('BitStarz', $casino->name);
        }
    }

    public function testSearchByRatingOperators()
    {
        $searchModel = new CasinoSearch();

        // Test > 4.5
        $dataProvider = $searchModel->search(['CasinoSearch' => ['rating' => '>4.5']]);
        $models = $dataProvider->getModels();
        foreach ($models as $casino) {
            $this->assertGreaterThan(4.5, (float)$casino->rating);
        }

        // Test <= 4.0
        $dataProvider = $searchModel->search(['CasinoSearch' => ['rating' => '<=4.0']]);
        $models = $dataProvider->getModels();
        foreach ($models as $casino) {
            $this->assertLessThanOrEqual(4.0, (float)$casino->rating);
        }
    }

    public function testSearchByIsActive()
    {
        $searchModel = new CasinoSearch();
        $dataProvider = $searchModel->search(['CasinoSearch' => ['is_active' => '1']]);
        $models = $dataProvider->getModels();

        $this->assertNotEmpty($models);
        foreach ($models as $casino) {
            $this->assertEquals(1, $casino->is_active);
        }
    }
}
