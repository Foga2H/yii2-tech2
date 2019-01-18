<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "animal".
 *
 * @property int $id
 * @property int $animal_type_id
 * @property int $user_id
 * @property int $animal_parent_id
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
     * @param int $animal_id
     * @param int $hearts
     * @return bool|null
     */
    public static function addHearts($animal_id, $hearts)
    {
        if ($animal = static::findOne(['id' => $animal_id])) {
            $animal->hearts = $animal->hearts + $hearts;

            return $animal->save();
        }

        return null;
    }

    /**
     * @param $animal_id
     * @param $hearts
     * @return bool|null
     */
    public static function setHearts($animal_id, $hearts)
    {
        if ($animal = static::findOne(['id' => $animal_id]) && $hearts) {
            $animal->hearts = $hearts;

            return $animal->save();
        }

        return null;
    }

    /**
     * @param $user
     */
    public static function tryChildbirthByUser($user)
    {
        $animals = Animal::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['>', 'age', 0])
            ->all();

        $males = array_filter($animals, function($item) { return $item['sex'] === self::SEX_MALE; });
        $females = array_filter($animals, function($item) { return $item['sex'] === self::SEX_FEMALE; });

        if ($males && $females) {
            $male = $males(array_rand($males));

            $childs = static::getChilds($male, $animals);
            $parents = static::getParents($male, $animals);

            $filterFemales = array_filter($females, function($item) use ($childs, $parents, $male) {
                return $item['animal_type_id'] === $male->animal_type_id
                    && !in_array($item['id'], $childs)
                    && !in_array($item['id'], $parents);
            });

            if ($filterFemales) {
                $female = $filterFemales(array_rand($filterFemales));

                $animal = new Animal();
                $animal->animal_type_id = $male->animal_type_id;
                $animal->animal_parent_id = $male->id;
                $animal->user_id = $user->id;
                $animal->hearts = $male->getAnimalType()->one()->hearts_by_default;
                $animal->sex = self::randomSex();
                $animal->created_at = time();
                $animal->updated_at = time();
                $animal->save();
            }
        }
    }

    /**
     * @param Animal $animal
     * @return array
     */
    public static function findParents($animal)
    {
        $animals = static::find()->all();

        $parents = self::getParents($animal, $animals);

        return $parents;
    }

    /**
     * @param Animal $animal
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findChilds($animal)
    {
        $animals = static::find()->all();

        $childs = self::getChilds($animal, $animals);

        return $childs;
    }

    /**
     * @return mixed
     */
    public static function randomSex()
    {
        $sex = self::getSexTypes();

        return $sex[array_rand($sex)];
    }

    /**
     * @param $animal
     * @param $animals
     * @param array $parents
     * @return array
     */
    private static function getParents($animal, $animals, $parents = [])
    {
        if (!is_null($animal->animal_parent_id)) {
            $parents[] = $animal->animal_parent_id;

            if ($findParents = static::find()->where(['animal_parent_id' => $animal->animal_parent_id])
                ->andWhere(['!=', 'id', $animal->id])->all()) {

                foreach ($findParents as $parent) {
                    foreach (self::getParents($parent, $animals, $parents) as $p) {
                        if (!in_array($p, $parents)) {
                            $parents[] = $p;
                        }
                    }
                }
            }
        }

        return $parents;
    }

    /**
     * @param $animal
     * @param array $animals
     * @param array $childs
     * @return array|\yii\db\ActiveRecord[]
     */
    private static function getChilds($animal, $animals, $childs = [])
    {
        if ($findChilds = static::find()->where(['animal_parent_id' => $animal->id])->all()) {
            foreach ($findChilds as $child) {
                $childs[] = $child->id;

                foreach (self::getChilds($child, $animals, $childs) as $c) {
                    if (!in_array($c, $childs)) {
                        $childs[] = $c;
                    }
                }
            }
        }

        return $childs;
    }
}
