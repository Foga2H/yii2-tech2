<?php

namespace common\models;

use Codeception\Exception\ElementNotFound;
use yii\base\ErrorException;

/**
 * This is the model class for table "auction".
 *
 * @property int $id
 * @property int $animal_id
 * @property int $price
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Animal $animal
 */
class Auction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['animal_id', 'price', 'created_at', 'updated_at'], 'required'],
            [['animal_id', 'price', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['animal_id', 'price', 'created_at', 'updated_at'], 'integer'],
            [['animal_id'], 'unique'],
            [['animal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Animal::className(), 'targetAttribute' => ['animal_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'animal_id' => 'Animal ID',
            'price' => 'Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnimal()
    {
        return $this->hasOne(Animal::class, ['id' => 'animal_id']);
    }

    /**
     * @param $animal_id
     * @param $price
     * @return bool
     * @throws ErrorException
     */
    public static function createItem($animal_id, $price)
    {
        $animal = Animal::findOne(['id' => $animal_id]);

        if ($animal && !$animal->getAuction()->one()) {
            $auction = new Auction();
            $auction->animal_id = $animal_id;
            $auction->price = $price;
            $auction->created_at = time();
            $auction->updated_at = time();

            return $auction->save();
        }

        throw new ErrorException('Animal not found / Already on auction');
    }

    /**
     * @param $item_id
     * @return array|null|\yii\db\ActiveRecord
     * @throws ErrorException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function buyItem($item_id)
    {
        if ($item = static::findOne(['id' => $item_id])) {
            $user = User::findOne(['id' => \Yii::$app->user->id]);

            if ($item->price <= $user->getHearts()) {
                if ($animal = $item->getAnimal()->one()) {

                    if ($animal->user_id === \Yii::$app->user->id) {
                        throw new ErrorException('You cant buy from yourself');
                    }

                    User::addHearts($animal->user_id, $item->price);

                    $animal->user_id = \Yii::$app->user->id;
                    $animal->save();

                    User::setHearts(\Yii::$app->user->id, $user->getHearts() - $item->price);

                    $item->delete();

                    return $animal;
                }
            } else {
                throw new ErrorException('You havent enough hearts');
            }
        }

        throw new ErrorException('Auction not found');
    }
}
