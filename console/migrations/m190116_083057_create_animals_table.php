<?php

use yii\db\Migration;

/**
 * Handles the creation of table `animals`.
 */
class m190116_083057_create_animals_table extends Migration
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

        $this->createTable('{{%animal}}', [
            'id' => $this->primaryKey(),
            'animal_type_id' => $this->integer()->notNull(),
            'animal_parent_id' => $this->integer()->defaultValue(null),
            'user_id' => $this->integer()->notNull(),
            'sex' => $this->integer()->notNull(),
            'age' => $this->integer()->notNull()->defaultValue(0),
            'hearts' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull()
        ], $tableOptions);

        $this->createIndex(
            'idx-animals-animal_type_id',
            '{{%animal}}',
            'animal_type_id'
        );

        $this->addForeignKey(
            'fk-animals-animal_type_id',
            '{{%animal}}',
            'animal_type_id',
            '{{%animal_types}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-animals-animal_parent_id',
            '{{%animal}}',
            'animal_parent_id'
        );

        $this->addForeignKey(
            'fk-animals-animal_parent_id',
            '{{%animal}}',
            'animal_parent_id',
            '{{%animal}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-animals-user_id',
            '{{%animal}}',
            'user_id'
        );

        $this->addForeignKey(
            'fk-animals-user_id',
            '{{%animal}}',
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
            'fk-animals-animal_type_id',
            '{{%animal}}'
        );

        $this->dropIndex(
            'idx-animals-animal_type_id',
            '{{%animal}}'
        );

        $this->dropForeignKey(
            'fk-animals-animal_parent_id',
            '{{%animal}}'
        );

        $this->dropIndex(
            'idx-animals-animal_parent_id',
            '{{%animal}}'
        );

        $this->dropForeignKey(
            'fk-animals-user_id',
            '{{%animal}}'
        );

        $this->dropIndex(
            'idx-animals-user_id',
            '{{%animal}}'
        );

        $this->dropTable('{{%animal}}');
    }
}
