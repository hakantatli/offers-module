<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Seeds initial demo data for casinos and offers.
 * Executed via optional seed migration command.
 */
class m260923_200001_seed_initial_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $now = date('Y-m-d H:i:s');
        $futureDate1 = date('Y-m-d H:i:s', strtotime('+30 days'));
        $futureDate2 = date('Y-m-d H:i:s', strtotime('+60 days'));
        $futureDate3 = date('Y-m-d H:i:s', strtotime('+90 days'));
        $pastDate = date('Y-m-d H:i:s', strtotime('-15 days'));

        // 1. Seed Casinos (6 active, 1 inactive)
        $casinos = [
            ['name' => 'BitStarz Club', 'slug' => 'bitstarz-club', 'rating' => 4.90, 'is_active' => 1, 'created_at' => $now],
            ['name' => 'Casino Royale', 'slug' => 'casino-royale', 'rating' => 4.85, 'is_active' => 1, 'created_at' => $now],
            ['name' => 'LeoVegas Grand', 'slug' => 'leovegas-grand', 'rating' => 4.75, 'is_active' => 1, 'created_at' => $now],
            ['name' => '7Bit Casino', 'slug' => '7bit-casino', 'rating' => 4.60, 'is_active' => 1, 'created_at' => $now],
            ['name' => 'Stake Lounge', 'slug' => 'stake-lounge', 'rating' => 4.50, 'is_active' => 1, 'created_at' => $now],
            ['name' => 'Fortune Jack', 'slug' => 'fortune-jack', 'rating' => 4.35, 'is_active' => 1, 'created_at' => $now],
            ['name' => 'Golden Palace (Inactive)', 'slug' => 'golden-palace', 'rating' => 3.80, 'is_active' => 0, 'created_at' => $now],
        ];

        $casinoIdMap = [];
        foreach ($casinos as $c) {
            $this->insert('{{%casino}}', $c);
            $id = (new Query())
                ->select('id')
                ->from('{{%casino}}')
                ->where(['slug' => $c['slug']])
                ->scalar();
            $casinoIdMap[$c['slug']] = $id;
        }

        // 2. Seed Offers linked dynamically via casino slug
        $offers = [
            // BitStarz Club
            ['bitstarz-club', '100% Match Welcome Bonus up to €500', 'bitstarz-100-welcome-bonus-500', 'welcome', 500.00, '40x wagering requirement. Min deposit €20. Valid for 30 days.', $futureDate1, 'active'],
            ['bitstarz-club', '20 Exclusive Free Spins on Sign Up', 'bitstarz-20-exclusive-free-spins', 'no_deposit', 20.00, 'Max cashout €100. 35x wagering on winnings. No deposit needed.', $futureDate2, 'active'],
            ['bitstarz-club', '180 Bonus Spins on First 4 Deposits', 'bitstarz-180-bonus-spins-first-deposits', 'free_spins', 180.00, 'Distributed over 9 days (20 spins/day). Min deposit €20.', null, 'active'],
            ['bitstarz-club', 'VIP Reload Bonus 50% up to €1000', 'bitstarz-vip-reload-bonus-1000', 'welcome', 1000.00, 'Available every Monday. 40x wagering. Min deposit €50.', $futureDate3, 'active'],

            // Casino Royale
            ['casino-royale', 'Royal Welcome Package €1500 + 150 Spins', 'royal-welcome-package-1500-150-spins', 'welcome', 1500.00, 'Split across first 3 deposits. 35x wagering requirement.', $futureDate2, 'active'],
            ['casino-royale', '€25 Free Chip No Deposit Required', 'royal-25-free-chip-no-deposit', 'no_deposit', 25.00, 'New players only. Max conversion 5x bonus. 45x wager.', $futureDate1, 'active'],
            ['casino-royale', '50 Royal Free Spins on Book of Dead', 'royal-50-free-spins-book-of-dead', 'free_spins', 50.00, 'Spin value €0.20 each. 30x wagering on free spin wins.', null, 'active'],
            ['casino-royale', 'Weekend High Roller Bonus €2000', 'royal-weekend-high-roller-2000', 'welcome', 2000.00, '50% match bonus on deposits over €500. 35x wager.', $futureDate3, 'active'],

            // LeoVegas Grand
            ['leovegas-grand', 'King of Casino Welcome Offer €1000', 'leovegas-king-of-casino-welcome-1000', 'welcome', 1000.00, '100% deposit match up to €1000. Real cash rewards.', $futureDate1, 'active'],
            ['leovegas-grand', '50 Cash Free Spins No Wagering', 'leovegas-50-cash-free-spins-no-wagering', 'free_spins', 50.00, 'Winnings paid directly in cash. No wagering needed.', null, 'active'],
            ['leovegas-grand', '€10 Live Casino Free Chip', 'leovegas-10-live-casino-free-chip', 'no_deposit', 10.00, 'Valid on Evolution Live tables only. 40x wagering.', $futureDate2, 'active'],
            ['leovegas-grand', 'Exclusive Midweek €300 Reload', 'leovegas-exclusive-midweek-300-reload', 'welcome', 300.00, 'Claimable on Wednesdays. 35x playthrough requirement.', $futureDate3, 'active'],

            // 7Bit Casino
            ['7bit-casino', '100% up to €300 or 1.5 BTC Welcome', '7bit-100-up-to-300-welcome', 'welcome', 300.00, '40x wagering requirement. Crypto deposits supported.', $futureDate1, 'active'],
            ['7bit-casino', '75 Free Spins on Aloha King Elvis', '7bit-75-free-spins-aloha-king-elvis', 'free_spins', 75.00, 'Valid on BGaming slots. 45x wagering on winnings.', null, 'active'],
            ['7bit-casino', '€15 Registration No Deposit Bonus', '7bit-15-registration-no-deposit-bonus', 'no_deposit', 15.00, 'Verify email to claim. Max withdrawal €50.', $futureDate2, 'active'],
            ['7bit-casino', 'Daily Cashback up to 15%', '7bit-daily-cashback-up-to-15', 'welcome', 150.00, 'Cashback credited daily based on previous day losses.', null, 'active'],

            // Stake Lounge
            ['stake-lounge', '200% Exclusive Crypto Welcome up to €1200', 'stake-200-exclusive-welcome-1200', 'welcome', 1200.00, '40x turnover. Instant deposit via BTC, ETH, USDT.', $futureDate2, 'active'],
            ['stake-lounge', '100 Free Spins on Sweet Bonanza', 'stake-100-free-spins-sweet-bonanza', 'free_spins', 100.00, 'Pragmatic Play exclusive. 30x wagering requirement.', $futureDate1, 'active'],
            ['stake-lounge', '€30 Stake Crypto No Deposit Drop', 'stake-30-crypto-no-deposit-drop', 'no_deposit', 30.00, 'Community bonus code drop. 40x wagering requirement.', $futureDate3, 'active'],
            ['stake-lounge', 'Monthly VIP Boost €750', 'stake-monthly-vip-boost-750', 'welcome', 750.00, 'Calculated based on monthly wagered volume. 1x wager.', null, 'active'],

            // Fortune Jack
            ['fortune-jack', 'Welcome Package 6 BTC or €1200 + 250 FS', 'fortune-jack-welcome-package-6btc', 'welcome', 1200.00, 'Split across first 4 deposits. 30x wagering.', $futureDate3, 'active'],
            ['fortune-jack', '100 Registration Free Spins No Deposit', 'fortune-jack-100-registration-free-spins', 'no_deposit', 100.00, 'Verify phone and email to unlock. 30x wager.', $futureDate1, 'active'],
            ['fortune-jack', 'Weekly Reload 50% up to €400', 'fortune-jack-weekly-reload-400', 'welcome', 400.00, 'Deposit on weekends. 35x wagering requirement.', null, 'active'],
            ['fortune-jack', '50 Free Spins on Starburst', 'fortune-jack-50-free-spins-starburst', 'free_spins', 50.00, 'NetEnt classic. Max win €200. 35x wagering.', $futureDate2, 'active'],

            // Draft offers
            ['bitstarz-club', 'Draft: Summer Fest Bonus €500', 'draft-summer-fest-bonus-500', 'welcome', 500.00, 'Pending marketing approval.', $futureDate1, 'draft'],
            ['casino-royale', 'Draft: Black Friday 200 Free Spins', 'draft-black-friday-200-free-spins', 'free_spins', 200.00, 'Scheduled for November.', $futureDate3, 'draft'],

            // Expired offers
            ['leovegas-grand', 'Expired: New Year 2026 Special Bonus', 'expired-new-year-2026-special-bonus', 'welcome', 250.00, 'Expired campaign.', $pastDate, 'active'],
            ['7bit-casino', 'Explicitly Expired Spring Bonus', 'explicitly-expired-spring-bonus', 'no_deposit', 50.00, 'Expired status.', $futureDate1, 'expired'],

            // Inactive Casino (Golden Palace)
            ['golden-palace', 'Golden Palace Inactive Welcome €800', 'golden-palace-inactive-welcome-800', 'welcome', 800.00, 'Casino is currently inactive.', $futureDate1, 'active'],
            ['golden-palace', 'Golden Palace 50 Inactive Spins', 'golden-palace-50-inactive-spins', 'free_spins', 50.00, 'Casino is currently inactive.', null, 'active'],
        ];

        foreach ($offers as $o) {
            $casinoSlug = $o[0];
            $casinoId = $casinoIdMap[$casinoSlug] ?? null;
            if (!$casinoId) {
                continue;
            }

            $this->insert('{{%offer}}', [
                'casino_id' => $casinoId,
                'title' => $o[1],
                'slug' => $o[2],
                'type' => $o[3],
                'amount' => $o[4],
                'terms' => $o[5],
                'expires_at' => $o[6],
                'status' => $o[7],
                'created_at' => $now,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%offer}}');
        $this->delete('{{%casino}}');
    }
}
