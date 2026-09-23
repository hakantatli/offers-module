<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%casino}}`.
 */
class m260923_100001_create_casino_table extends Migration
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

        $this->createTable('{{%casino}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'rating' => $this->decimal(3, 2)->notNull()->defaultValue(0.00),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->createIndex(
            'idx-casino-is_active',
            '{{%casino}}',
            'is_active'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%casino}}');
    }
}
