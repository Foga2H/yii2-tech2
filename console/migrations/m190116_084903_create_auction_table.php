<?php

use yii\db\Migration;

/**
 * Handles the creation of table `auction`.
 */
class m190116_084903_create_auction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%auction}}', [
            'id' => $this->primaryKey(),
            'animal_id' => $this->integer()->notNull()->unique(),
            'price' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull()
        ], $tableOptions);

        $this->createIndex(
            'idx-auction-animal_id',
            '{{%auction}}',
            'animal_id'
        );

        $this->addForeignKey(
            'fk-auction-animal_id',
            '{{%auction}}',
            'animal_id',
            '{{%animal}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-auction-animal_id',
            '{{%auction}}'
        );

        $this->dropIndex(
            'idx-auction-animal_id',
            '{{%auction}}'
        );

        $this->dropTable('{{%auction}}');
    }
}
