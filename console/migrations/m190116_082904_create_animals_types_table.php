<?php

use yii\db\Migration;

/**
 * Handles the creation of table `animals_types`.
 */
class m190116_082904_create_animals_types_table extends Migration
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

        $this->createTable('{{%animal_types}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'hearts_by_default' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull()
        ], $tableOptions);

        $this->insert('{{%animal_types}}', [
            'name' => 'Кролик',
            'hearts_by_default' => 3,
            'created_at' => time(),
            'updated_at' => time()
        ]);

        $this->insert('{{%animal_types}}', [
            'name' => 'Кот',
            'hearts_by_default' => 4,
            'created_at' => time(),
            'updated_at' => time()
        ]);

        $this->insert('{{%animal_types}}', [
            'name' => 'Пес',
            'hearts_by_default' => 5,
            'created_at' => time(),
            'updated_at' => time()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%animal_types}}');
    }
}
