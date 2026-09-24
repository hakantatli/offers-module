<?php

namespace app\tests\functional;

use PHPUnit\Framework\TestCase;
use app\models\Casino;
use app\models\Offer;

class AdminOfferEndpointTest extends TestCase
{
    private TestHttpClient $guestClient;
    private TestHttpClient $adminClient;
    private ?Casino $casino = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guestClient = new TestHttpClient();
        $this->adminClient = new TestHttpClient();

        // Log in admin client
        $this->adminClient->get('/login');
        $this->adminClient->post('/login', [
            'LoginForm[username]' => 'admin',
            'LoginForm[password]' => 'admin123',
        ]);

        $this->casino = Casino::find()->one();
    }

    protected function tearDown(): void
    {
        Offer::deleteAll(['like', 'title', 'Functional Test Offer']);
        parent::tearDown();
    }

    public function testOffersIndexGuestRedirects(): void
    {
        $resOffers = $this->guestClient->get('/admin/offers');
        $this->assertEquals(302, $resOffers['statusCode']);
        $this->assertStringContainsString('login', $resOffers['headers']['location'] ?? '');

        // Test /admin shortcut route
        $resAdmin = $this->guestClient->get('/admin');
        $this->assertEquals(302, $resAdmin['statusCode']);
        $this->assertStringContainsString('login', $resAdmin['headers']['location'] ?? '');
    }

    public function testOffersIndexAdminAccess(): void
    {
        $response = $this->adminClient->get('/admin/offers');
        $this->assertEquals(200, $response['statusCode']);
        $this->assertStringContainsString('Manage Offers', $response['body']);
        $this->assertStringContainsString('+ New Offer', $response['body']);
    }

    public function testOfferCreateForm(): void
    {
        // Guest blocked
        $guestRes = $this->guestClient->get('/admin/offers/create');
        $this->assertEquals(302, $guestRes['statusCode']);

        // Admin gets form
        $adminRes = $this->adminClient->get('/admin/offers/create');
        $this->assertEquals(200, $adminRes['statusCode']);
        $this->assertStringContainsString('Create Offer', $adminRes['body']);
        $this->assertStringContainsString('name="Offer[title]"', $adminRes['body']);
    }

    public function testOfferCreateValidationFailurePastExpiry(): void
    {
        $this->adminClient->get('/admin/offers/create');

        $pastDate = date('Y-m-d\TH:i', strtotime('-2 days'));

        $response = $this->adminClient->post('/admin/offers/create', [
            'Offer[casino_id]' => $this->casino?->id,
            'Offer[title]' => 'Functional Test Offer Past Expiry',
            'Offer[type]' => Offer::TYPE_WELCOME,
            'Offer[amount]' => '150.00',
            'Offer[terms]' => '20x wagering',
            'Offer[expires_at]' => $pastDate,
            'Offer[status]' => Offer::STATUS_ACTIVE,
        ]);

        $this->assertEquals(200, $response['statusCode']);
        $this->assertStringContainsString('Expiration date must be in the future.', $response['body']);
    }

    public function testOfferCreateSuccess(): void
    {
        $this->adminClient->get('/admin/offers/create');

        $futureDate = date('Y-m-d\TH:i', strtotime('+15 days'));

        $response = $this->adminClient->post('/admin/offers/create', [
            'Offer[casino_id]' => $this->casino?->id,
            'Offer[title]' => 'Functional Test Offer Special Promo',
            'Offer[type]' => Offer::TYPE_NO_DEPOSIT,
            'Offer[amount]' => '25.00',
            'Offer[terms]' => '40x wagering on slots only',
            'Offer[expires_at]' => $futureDate,
            'Offer[status]' => Offer::STATUS_ACTIVE,
        ]);

        $this->assertEquals(302, $response['statusCode']);

        $offer = Offer::findOne(['title' => 'Functional Test Offer Special Promo']);
        $this->assertNotNull($offer);
        $this->assertEquals('functional-test-offer-special-promo', $offer->slug);
    }

    public function testOfferView(): void
    {
        $offer = Offer::find()->one();
        $this->assertNotNull($offer);

        $response = $this->adminClient->get('/admin/offers/' . $offer->id);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertStringContainsString($offer->title, $response['body']);

        // Non-existent ID returns 404
        $notFound = $this->adminClient->get('/admin/offers/999999');
        $this->assertEquals(404, $notFound['statusCode']);
    }

    public function testOfferUpdate(): void
    {
        $offer = new Offer([
            'casino_id' => $this->casino?->id,
            'title' => 'Functional Test Offer To Update',
            'type' => Offer::TYPE_FREE_SPINS,
            'amount' => 50,
            'terms' => 'Wager terms',
            'status' => Offer::STATUS_DRAFT,
        ]);
        $offer->save();

        // 1. GET update form
        $formRes = $this->adminClient->get('/admin/offers/' . $offer->id . '/update');
        $this->assertEquals(200, $formRes['statusCode']);
        $this->assertStringContainsString('Update Offer: ' . $offer->title, $formRes['body']);

        // 2. POST update
        $updateRes = $this->adminClient->post('/admin/offers/' . $offer->id . '/update', [
            'Offer[casino_id]' => $this->casino?->id,
            'Offer[title]' => 'Functional Test Offer Updated Title',
            'Offer[type]' => Offer::TYPE_WELCOME,
            'Offer[amount]' => '250.00',
            'Offer[terms]' => 'Updated terms',
            'Offer[status]' => Offer::STATUS_ACTIVE,
        ]);
        $this->assertEquals(302, $updateRes['statusCode']);

        $offer->refresh();
        $this->assertEquals('Functional Test Offer Updated Title', $offer->title);
        $this->assertEquals(250.00, (float)$offer->amount);
    }

    public function testOfferDeleteVerbFilterAndExecution(): void
    {
        $offer = new Offer([
            'casino_id' => $this->casino?->id,
            'title' => 'Functional Test Offer To Delete',
            'type' => Offer::TYPE_FREE_SPINS,
            'amount' => 10,
            'terms' => 'Terms',
            'status' => Offer::STATUS_ACTIVE,
        ]);
        $offer->save();
        $offerId = $offer->id;

        // GET request must return 405 Method Not Allowed
        $getRes = $this->adminClient->get('/admin/offers/' . $offerId . '/delete');
        $this->assertEquals(405, $getRes['statusCode']);

        // POST request must delete and redirect (302)
        $this->adminClient->get('/admin/offers');
        $postRes = $this->adminClient->post('/admin/offers/' . $offerId . '/delete');
        $this->assertEquals(302, $postRes['statusCode']);

        $this->assertNull(Offer::findOne($offerId));
    }
}
