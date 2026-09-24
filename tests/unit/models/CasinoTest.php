<?php

namespace app\tests\unit\models;

use PHPUnit\Framework\TestCase;
use app\models\Casino;
use app\models\Offer;
use Yii;

class CasinoTest extends TestCase
{
    protected function tearDown(): void
    {
        // Cleanup any test casinos created during tests
        Casino::deleteAll(['like', 'name', 'Test Casino']);
        parent::tearDown();
    }

    public function testValidationRulesRequired(): void
    {
        $casino = new Casino();
        $this->assertFalse($casino->validate());
        $this->assertArrayHasKey('name', $casino->errors);
        $this->assertArrayHasKey('rating', $casino->errors);
    }

    public function testRatingValidationBounds(): void
    {
        // Negative rating should fail
        $casino = new Casino(['name' => 'Test Casino', 'rating' => -0.5]);
        $this->assertFalse($casino->validate(['rating']));
        $this->assertArrayHasKey('rating', $casino->errors);

        // Rating > 5.00 should fail
        $casino = new Casino(['name' => 'Test Casino', 'rating' => 5.5]);
        $this->assertFalse($casino->validate(['rating']));
        $this->assertArrayHasKey('rating', $casino->errors);

        // Non-numeric rating should fail
        $casino = new Casino(['name' => 'Test Casino', 'rating' => 'not-a-number']);
        $this->assertFalse($casino->validate(['rating']));

        // Valid ratings (0.00, 3.75, 5.00) should pass
        foreach ([0.0, 3.75, 5.0] as $validRating) {
            $casino = new Casino(['name' => 'Test Casino', 'rating' => $validRating]);
            $this->assertTrue($casino->validate(['rating']), "Rating $validRating should be valid.");
        }
    }

    public function testSluggableBehaviorAutoGeneratesSlug(): void
    {
        $casino = new Casino();
        $casino->name = 'Test Casino Royal 2026';
        $casino->rating = 4.80;
        $casino->is_active = 1;

        $this->assertTrue($casino->save(), 'Casino should save successfully: ' . json_encode($casino->errors));
        $this->assertEquals('test-casino-royal-2026', $casino->slug);
    }

    public function testSluggableBehaviorEnsuresUniqueSlugOnConflict(): void
    {
        $casino1 = new Casino(['name' => 'Test Casino Duplicate', 'rating' => 4.0, 'is_active' => 1]);
        $this->assertTrue($casino1->save());
        $this->assertEquals('test-casino-duplicate', $casino1->slug);

        $casino2 = new Casino(['name' => 'Test Casino Duplicate', 'rating' => 4.5, 'is_active' => 1]);
        $this->assertTrue($casino2->save());
        $this->assertEquals('test-casino-duplicate-2', $casino2->slug);
    }

    public function testManualSlugIsPreserved(): void
    {
        $casino = new Casino([
            'name' => 'Test Casino Custom',
            'slug' => 'my-custom-vip-slug',
            'rating' => 4.2,
            'is_active' => 1,
        ]);
        $this->assertTrue($casino->save());
        $this->assertEquals('my-custom-vip-slug', $casino->slug);
    }

    public function testOffersRelation(): void
    {
        $casino = Casino::find()->one();
        $this->assertNotNull($casino, 'At least one casino should exist in the database');
        $query = $casino->getOffers();
        $this->assertInstanceOf(\yii\db\ActiveQuery::class, $query);
    }
}
