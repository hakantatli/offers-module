<?php

namespace app\tests\functional;

use PHPUnit\Framework\TestCase;
use app\models\Casino;
use app\models\Offer;
use yii\db\Expression;

class PublicEndpointTest extends TestCase
{
    private TestHttpClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new TestHttpClient();
    }

    public function testRootRedirectsToOffers(): void
    {
        $response = $this->client->get('/');
        $this->assertEquals(302, $response['statusCode']);
        $this->assertStringContainsString('offers', $response['headers']['location'] ?? '');
    }

    public function testOffersIndexDisplaysCardsAndFilters(): void
    {
        $response = $this->client->get('/offers');
        $this->assertEquals(200, $response['statusCode']);
        $this->assertStringContainsString('Top Verified Casino Offers', $response['body']);
        $this->assertStringContainsString('Filter by Casino', $response['body']);
        $this->assertStringContainsString('Filter by Offer Type', $response['body']);
        $this->assertStringContainsString('Showing <strong>20</strong>', $response['body']);
    }

    public function testOffersPaginationPageTwo(): void
    {
        $response = $this->client->get('/offers?page=2');
        $this->assertEquals(200, $response['statusCode']);
        $this->assertStringContainsString('Page <strong>2</strong>', $response['body']);
    }

    public function testOffersFilterByType(): void
    {
        $response = $this->client->get('/offers?type=no_deposit');
        $this->assertEquals(200, $response['statusCode']);
        $this->assertStringContainsString('(filtered)', $response['body']);
        $this->assertStringContainsString('No Deposit', $response['body']);
    }

    public function testOffersFilterByCasino(): void
    {
        $casino = Casino::find()->where(['is_active' => 1])->one();
        $this->assertNotNull($casino);

        $response = $this->client->get('/offers?casino_id=' . $casino->id);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertStringContainsString('(filtered)', $response['body']);
    }

    public function testOfferDetailActiveOfferReturns200(): void
    {
        $activeOffer = Offer::find()
            ->joinWith(['casino'])
            ->where(['offer.status' => Offer::STATUS_ACTIVE, 'casino.is_active' => 1])
            ->andWhere(['or', ['offer.expires_at' => null], ['>', 'offer.expires_at', new Expression('NOW()')]])
            ->one();

        $this->assertNotNull($activeOffer);

        $response = $this->client->get('/offer/' . $activeOffer->slug);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertStringContainsString($activeOffer->title, $response['body']);
        $this->assertStringContainsString($activeOffer->casino->name, $response['body']);
        $this->assertStringContainsString('Terms & Wagering Requirements', $response['body']);
    }

    public function testOfferDetailNonExistentSlugReturns404(): void
    {
        $response = $this->client->get('/offer/this-offer-slug-does-not-exist-at-all');
        $this->assertEquals(404, $response['statusCode']);
    }

    public function testOfferDetailDraftStatusReturns404(): void
    {
        $draftOffer = Offer::findOne(['status' => Offer::STATUS_DRAFT]);
        if ($draftOffer) {
            $response = $this->client->get('/offer/' . $draftOffer->slug);
            $this->assertEquals(404, $response['statusCode']);
        }
    }

    public function testOfferDetailExpiredStatusReturns404(): void
    {
        $expiredOffer = Offer::findOne(['status' => Offer::STATUS_EXPIRED]);
        if ($expiredOffer) {
            $response = $this->client->get('/offer/' . $expiredOffer->slug);
            $this->assertEquals(404, $response['statusCode']);
        }
    }

    public function testOfferDetailInactiveCasinoReturns404(): void
    {
        $inactiveCasinoOffer = Offer::find()
            ->joinWith(['casino'])
            ->where(['casino.is_active' => 0])
            ->one();

        if ($inactiveCasinoOffer) {
            $response = $this->client->get('/offer/' . $inactiveCasinoOffer->slug);
            $this->assertEquals(404, $response['statusCode']);
        }
    }
}
