<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "animal_types".
 *
 * @property int $id
 * @property string $name
 * @property int $hearts_by_default
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Animal[] $animals
 */
class AnimalTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'animal_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'hearts_by_default', 'created_at', 'updated_at'], 'required'],
            [['hearts_by_default', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['hearts_by_default', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'hearts_by_default' => 'Hearts By Default',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnimals()
    {
        return $this->hasMany(Animal::className(), ['animal_type_id' => 'id']);
    }
}
