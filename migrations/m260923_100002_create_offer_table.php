<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%offer}}`.
 * Has foreign keys to the tables:
 * - `{{%casino}}`
 */
class m260923_100002_create_offer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%offer}}', [
            'id' => $this->primaryKey(),
            'casino_id' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'type' => $this->string(50)->notNull(),
            'amount' => $this->decimal(10, 2)->notNull(),
            'terms' => $this->text()->notNull(),
            'expires_at' => $this->dateTime()->null(),
            'status' => $this->string(20)->notNull()->defaultValue('draft'),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->null(),
        ], $tableOptions);

        // Index for casino_id
        $this->createIndex(
            'idx-offer-casino_id',
            '{{%offer}}',
            'casino_id'
        );

        // Foreign key to casino
        $this->addForeignKey(
            'fk-offer-casino_id',
            '{{%offer}}',
            'casino_id',
            '{{%casino}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Index for offer type
        $this->createIndex(
            'idx-offer-type',
            '{{%offer}}',
            'type'
        );

        // Composite index for public query performance (status + expires_at)
        $this->createIndex(
            'idx-offer-status-expires_at',
            '{{%offer}}',
            ['status', 'expires_at']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-offer-casino_id', '{{%offer}}');
        $this->dropTable('{{%offer}}');
    }
}
