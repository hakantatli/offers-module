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
        $this->assertStringContainsString('Casino Offers', $response['body']);
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

    public function testSitemapXmlReturnsValidXmlWithActiveCasinosAndOffers(): void
    {
        $response = $this->client->get('/sitemap.xml');
        $this->assertEquals(200, $response['statusCode']);
        $contentType = $response['headers']['content-type'] ?? '';
        $this->assertStringContainsString('application/xml', $contentType);

        // Verify valid XML structure
        $xml = simplexml_load_string($response['body']);
        $this->assertNotFalse($xml, 'Sitemap output must be valid XML');
        $this->assertEquals('urlset', $xml->getName());

        // Check namespace
        $namespaces = $xml->getNamespaces(true);
        $this->assertArrayHasKey('', $namespaces);
        $this->assertEquals('http://www.sitemaps.org/schemas/sitemap/0.9', $namespaces['']);

        // Collect all locations
        $locs = [];
        foreach ($xml->url as $url) {
            $loc = (string)$url->loc;
            $locs[] = $loc;
            if (isset($url->lastmod)) {
                $lastmod = (string)$url->lastmod;
                $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $lastmod);
            }
        }

        // Verify root /offers is included
        $this->assertTrue(
            array_filter($locs, fn($loc) => str_ends_with($loc, '/offers')) !== [],
            'Sitemap must include root /offers page'
        );

        // Verify active casinos are in sitemap
        $activeCasinos = Casino::find()->where(['is_active' => 1])->all();
        foreach ($activeCasinos as $casino) {
            $expectedCasinoLoc = '/casino/' . $casino->slug;
            $found = array_filter($locs, fn($loc) => str_contains($loc, $expectedCasinoLoc));
            $this->assertNotEmpty($found, "Active casino {$casino->name} must appear in sitemap");
        }

        // Verify active non-expired offers are in sitemap
        $activeOffers = Offer::find()
            ->joinWith(['casino'])
            ->where(['offer.status' => Offer::STATUS_ACTIVE, 'casino.is_active' => 1])
            ->andWhere(['or', ['offer.expires_at' => null], ['>', 'offer.expires_at', new Expression('NOW()')]])
            ->all();

        foreach ($activeOffers as $offer) {
            $expectedOfferLoc = '/offer/' . $offer->slug;
            $found = array_filter($locs, fn($loc) => str_contains($loc, $expectedOfferLoc));
            $this->assertNotEmpty($found, "Active offer {$offer->title} must appear in sitemap");
        }

        // Verify draft offers do NOT appear
        $draftOffers = Offer::find()->where(['status' => Offer::STATUS_DRAFT])->all();
        foreach ($draftOffers as $draftOffer) {
            $draftLoc = '/offer/' . $draftOffer->slug;
            $found = array_filter($locs, fn($loc) => str_contains($loc, $draftLoc));
            $this->assertEmpty($found, "Draft offer {$draftOffer->title} must NOT appear in sitemap");
        }

        // Verify expired offers do NOT appear
        $expiredOffers = Offer::find()->where(['status' => Offer::STATUS_EXPIRED])->all();
        foreach ($expiredOffers as $expiredOffer) {
            $expiredLoc = '/offer/' . $expiredOffer->slug;
            $found = array_filter($locs, fn($loc) => str_contains($loc, $expiredLoc));
            $this->assertEmpty($found, "Expired offer {$expiredOffer->title} must NOT appear in sitemap");
        }

        // Verify inactive casinos do NOT appear
        $inactiveCasinos = Casino::find()->where(['is_active' => 0])->all();
        foreach ($inactiveCasinos as $inactiveCasino) {
            $inactiveLoc = '/casino/' . $inactiveCasino->slug;
            $found = array_filter($locs, fn($loc) => str_contains($loc, $inactiveLoc));
            $this->assertEmpty($found, "Inactive casino {$inactiveCasino->name} must NOT appear in sitemap");
        }
    }

    public function testCasinoSlugEndpointReturnsActiveOffers(): void
    {
        $activeCasino = Casino::find()->where(['is_active' => 1])->one();
        $this->assertNotNull($activeCasino);

        $response = $this->client->get('/casino/' . $activeCasino->slug);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertStringContainsString('Casino Offers', $response['body']);
    }

    public function testCasinoSlugNonExistentReturns404(): void
    {
        $response = $this->client->get('/casino/this-casino-slug-does-not-exist-at-all');
        $this->assertEquals(404, $response['statusCode']);
    }

    public function testCasinoSlugInactiveCasinoReturns404(): void
    {
        $inactiveCasino = Casino::find()->where(['is_active' => 0])->one();
        if ($inactiveCasino) {
            $response = $this->client->get('/casino/' . $inactiveCasino->slug);
            $this->assertEquals(404, $response['statusCode']);
        }
    }
}
