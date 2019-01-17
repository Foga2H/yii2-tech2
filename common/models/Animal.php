<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "animal".
 *
 * @property int $id
 * @property int $animal_type_id
 * @property int $user_id
 * @property string $sex
 * @property int $age
 * @property int $hearts
 * @property int $created_at
 * @property int $updated_at
 *
 * @property AnimalTypes $animalType
 * @property Auction $auction
 */
class Animal extends \yii\db\ActiveRecord
{
    const SEX_FEMALE = 0;
    const SEX_MALE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'animal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['animal_type_id', 'user_id', 'sex', 'hearts'], 'required'],
            [['animal_type_id', 'hearts', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['animal_type_id', 'user_id', 'age', 'hearts', 'created_at', 'updated_at'], 'integer'],
            [['sex'], 'integer', 'max' => 255],
            [['animal_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => AnimalTypes::class, 'targetAttribute' => ['animal_type_id' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public static function getSexTypes()
    {
        return [
            self::SEX_FEMALE, self::SEX_MALE
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnimalType()
    {
        return $this->hasOne(AnimalTypes::class, ['id' => 'animal_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnimalUser()
    {
        return $this->hasOne(User::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuction()
    {
        return $this->hasOne(Auction::class, ['animal_id' => 'id']);
    }

    /**
     * @param User $user
     */
    public static function addStartAnimals(User $user)
    {
        $animalTypes = AnimalTypes::find()->all();

        foreach($animalTypes as $animalType) {
            $animal = new Animal();
            $animal->animal_type_id = $animalType->id;
            $animal->user_id = $user->id;
            $animal->hearts = $animalType->hearts_by_default;
            $animal->sex = self::randomSex();
            $animal->created_at = time();
            $animal->updated_at = time();
            $animal->save();
        }
    }

    /**
     * @return mixed
     */
    public static function randomSex() {
        $sex = self::getSexTypes();

        return $sex[array_rand($sex)];
    }
}
