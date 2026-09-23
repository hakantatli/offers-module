<?php

namespace app\tests\unit\models;

use PHPUnit\Framework\TestCase;
use app\models\Casino;
use app\models\Offer;
use Yii;

class OfferTest extends TestCase
{
    private ?Casino $testCasino = null;

    protected function setUp(): void
    {
        parent::setUp();
        // Create or fetch a test casino
        $this->testCasino = Casino::findOne(['slug' => 'test-unit-casino']);
        if (!$this->testCasino) {
            $this->testCasino = new Casino([
                'name' => 'Test Unit Casino',
                'slug' => 'test-unit-casino',
                'rating' => 4.5,
                'is_active' => 1,
            ]);
            $this->testCasino->save();
        }
    }

    protected function tearDown(): void
    {
        Offer::deleteAll(['like', 'title', 'Test Unit Offer']);
        parent::tearDown();
    }

    public static function tearDownAfterClass(): void
    {
        Casino::deleteAll(['slug' => 'test-unit-casino']);
    }

    public function testValidationRulesRequired()
    {
        $offer = new Offer();
        $this->assertFalse($offer->validate());
        $this->assertArrayHasKey('casino_id', $offer->errors);
        $this->assertArrayHasKey('title', $offer->errors);
        $this->assertArrayHasKey('type', $offer->errors);
        $this->assertArrayHasKey('amount', $offer->errors);
        $this->assertArrayHasKey('terms', $offer->errors);
        $this->assertArrayHasKey('status', $offer->errors);
    }

    public function testCasinoForeignKeyValidation()
    {
        $offer = new Offer([
            'casino_id' => 999999, // Non-existent casino
            'title' => 'Test Unit Offer FK',
            'type' => Offer::TYPE_WELCOME,
            'amount' => 100,
            'terms' => 'Standard terms',
            'status' => Offer::STATUS_ACTIVE,
        ]);
        $this->assertFalse($offer->validate(['casino_id']));
        $this->assertArrayHasKey('casino_id', $offer->errors);
    }

    public function testTypeEnumValidation()
    {
        $offer = new Offer(['type' => 'invalid_bonus_type']);
        $this->assertFalse($offer->validate(['type']));

        foreach ([Offer::TYPE_WELCOME, Offer::TYPE_NO_DEPOSIT, Offer::TYPE_FREE_SPINS] as $validType) {
            $offer = new Offer(['type' => $validType]);
            $this->assertTrue($offer->validate(['type']), "Type $validType should be valid");
        }
    }

    public function testStatusEnumValidation()
    {
        $offer = new Offer(['status' => 'archived_invalid']);
        $this->assertFalse($offer->validate(['status']));

        foreach ([Offer::STATUS_DRAFT, Offer::STATUS_ACTIVE, Offer::STATUS_EXPIRED] as $validStatus) {
            $offer = new Offer(['status' => $validStatus]);
            $this->assertTrue($offer->validate(['status']), "Status $validStatus should be valid");
        }
    }

    public function testAmountValidation()
    {
        $offer = new Offer(['amount' => -50]);
        $this->assertFalse($offer->validate(['amount']));

        $offer = new Offer(['amount' => 0]);
        $this->assertTrue($offer->validate(['amount']));

        $offer = new Offer(['amount' => 500.50]);
        $this->assertTrue($offer->validate(['amount']));
    }

    public function testExpiresAtValidationFutureRequirement()
    {
        // Past date must fail
        $pastDate = date('Y-m-d H:i:s', strtotime('-1 day'));
        $offer = new Offer([
            'casino_id' => $this->testCasino->id,
            'title' => 'Test Unit Offer Past Date',
            'type' => Offer::TYPE_WELCOME,
            'amount' => 100,
            'terms' => 'Terms',
            'status' => Offer::STATUS_ACTIVE,
            'expires_at' => $pastDate,
        ]);
        $this->assertFalse($offer->validate(['expires_at']));
        $this->assertEquals('Expiration date must be in the future.', $offer->getFirstError('expires_at'));

        // Future date must pass
        $futureDate = date('Y-m-d H:i:s', strtotime('+30 days'));
        $offer->expires_at = $futureDate;
        $this->assertTrue($offer->validate(['expires_at']));

        // Null (ongoing offer) must pass
        $offer->expires_at = null;
        $this->assertTrue($offer->validate(['expires_at']));

        // Invalid date format must fail
        $offer->expires_at = 'not-a-valid-datetime';
        $this->assertFalse($offer->validate(['expires_at']));
    }

    public function testSluggableBehaviorAutoGeneratesSlug()
    {
        $offer = new Offer([
            'casino_id' => $this->testCasino->id,
            'title' => 'Test Unit Offer Super Bonus 500',
            'type' => Offer::TYPE_WELCOME,
            'amount' => 500,
            'terms' => '35x wagering',
            'status' => Offer::STATUS_ACTIVE,
        ]);
        $this->assertTrue($offer->save(), 'Offer should save: ' . json_encode($offer->errors));
        $this->assertEquals('test-unit-offer-super-bonus-500', $offer->slug);
    }

    public function testManualSlugIsSanitized()
    {
        $offer = new Offer([
            'casino_id' => $this->testCasino->id,
            'title' => 'Test Unit Offer Manual Slug',
            'slug' => 'My Custom Slug With Spaces & Special @Chars!',
            'type' => Offer::TYPE_NO_DEPOSIT,
            'amount' => 20,
            'terms' => 'No deposit required',
            'status' => Offer::STATUS_ACTIVE,
        ]);
        $this->assertTrue($offer->save(), 'Offer should save: ' . json_encode($offer->errors));
        $this->assertEquals('my-custom-slug-with-spaces-special-chars', $offer->slug);
    }

    public function testBadgeHelperMethods()
    {
        $offer = new Offer(['type' => Offer::TYPE_WELCOME, 'status' => Offer::STATUS_ACTIVE]);
        $this->assertEquals('bg-primary', $offer->getTypeBadgeClass());
        $this->assertEquals('bg-success', $offer->getStatusBadgeClass());

        $offer->type = Offer::TYPE_NO_DEPOSIT;
        $offer->status = Offer::STATUS_DRAFT;
        $this->assertEquals('bg-success', $offer->getTypeBadgeClass());
        $this->assertEquals('bg-warning text-dark', $offer->getStatusBadgeClass());

        $offer->type = Offer::TYPE_FREE_SPINS;
        $offer->status = Offer::STATUS_EXPIRED;
        $this->assertEquals('bg-info text-dark', $offer->getTypeBadgeClass());
        $this->assertEquals('bg-danger', $offer->getStatusBadgeClass());
    }
}
