<?php

namespace app\tests\unit\performance;

use PHPUnit\Framework\TestCase;
use app\models\Offer;
use yii\db\Expression;
use Yii;

class NPlusOneTest extends TestCase
{
    public function testZeroNPlusOneQueriesOnOffersDirectory()
    {
        Yii::$app->db->enableProfiling = true;
        Yii::getLogger()->messages = []; // Clear log messages

        // Run the exact query used by OfferController::actionIndex
        $offers = Offer::find()
            ->joinWith(['casino'])
            ->where(['offer.status' => Offer::STATUS_ACTIVE])
            ->andWhere(['casino.is_active' => 1])
            ->andWhere([
                'or',
                ['offer.expires_at' => null],
                ['>', 'offer.expires_at', new Expression('NOW()')],
            ])
            ->with(['casino'])
            ->limit(20)
            ->all();

        $this->assertNotEmpty($offers);
        $this->assertCount(20, $offers, 'Expected exactly 20 offers loaded');

        $initialQueryCount = count(array_filter(
            Yii::getLogger()->messages,
            fn($m) => $m[2] === 'yii\\db\\Command::query'
        ));

        // Now simulate the view loop accessing casino properties for all 20 cards
        $casinoNames = [];
        $casinoRatings = [];
        foreach ($offers as $offer) {
            // If eager loading failed, this line would trigger an N+1 query: SELECT * FROM casino WHERE id = X
            $casinoNames[] = $offer->casino->name;
            $casinoRatings[] = $offer->casino->rating;
        }

        $finalQueryCount = count(array_filter(
            Yii::getLogger()->messages,
            fn($m) => $m[2] === 'yii\\db\\Command::query'
        ));

        // Assert that accessing $offer->casino in a loop produced ZERO additional queries!
        $this->assertEquals(
            $initialQueryCount,
            $finalQueryCount,
            'Accessing $offer->casino inside the loop caused additional SQL queries! (N+1 problem detected)'
        );

        $this->assertCount(20, $casinoNames);
        $this->assertCount(20, $casinoRatings);
    }
}
