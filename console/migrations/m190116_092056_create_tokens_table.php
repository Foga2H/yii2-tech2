<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tokens`.
 */
class m190116_092056_create_tokens_table extends Migration
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

        $this->createTable('{{%token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'token' => $this->string()->notNull()->unique(),
            'expired_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-tokens-user_id',
            '{{%token}}',
            'user_id'
        );

        $this->addForeignKey(
            'fk-tokens-user_id',
            '{{%token}}',
            'user_id',
            '{{%user}}',
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
            'fk-tokens-user_id',
            '{{%token}}'
        );

        $this->dropIndex(
            'idx-tokens-user_id',
            '{{%token}}'
        );

        $this->dropTable('{{%token}}');
    }
}
