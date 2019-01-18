<?php
namespace common\models;

use Yii;
use yii\base\ErrorException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $hearts
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const HEARTS_COUNT = 7; // Hearts count for one animal

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $access = Token::find()
            ->where(['token' => $token])
            ->one();

        return isset($access) ? $access->getUser()->one() : null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @return int
     */
    public function getHearts()
    {
        return $this->hearts;
    }

    /**
     * @param $user_id
     * @param $hearts
     * @return bool
     * @throws ErrorException
     */
    public static function setHearts($user_id, $hearts)
    {
        if ($user = static::findOne(['id' => $user_id])) {
            $user->hearts = $hearts;
            return $user->save();
        }

        throw new ErrorException('User not found');
    }

    /**
     * @param $user_id
     * @param $hearts
     * @return bool
     * @throws ErrorException
     */
    public static function addHearts($user_id, $hearts)
    {
        if ($user = static::findOne(['id' => $user_id])) {
            $user->hearts = $user->hearts + $hearts;
            return $user->save();
        }

        throw new ErrorException('User not found');
    }

    /**
     * @param $animalCount
     * @return User[]
     */
    public static function byAnimalCountAndType($animalCount, $animalType) {
        $animals = (new Query())->select('user_id, COUNT(*)')
            ->from('{{%animal}}')->where(['animal_type_id' => $animalType])->groupBy('user_id')->all();

        $users_ids = array_map(function($item) { return $item['user_id']; }, array_filter($animals, function($item) use ($animalCount) {
            return $item['count'] == $animalCount;
        }));

        return static::findAll($users_ids);
    }

    /**
     * @param $animalCount
     * @return User[]
     */
    public static function byAnimalCount($animalCount) {
        $animals = (new Query())->select('user_id, COUNT(*)')
            ->from('{{%animal}}')->groupBy('user_id')->all();

        $users_ids = array_map(function($item) { return $item['user_id']; }, array_filter($animals, function($item) use ($animalCount) {
            return $item['count'] == $animalCount;
        }));

        return static::findAll($users_ids);
    }

    /**
     * @param $user
     * @return bool
     * @throws ErrorException
     */
    public static function giveHeartsByAnimalCount($user) {
        if ($animalCount = Animal::find()->where(['user_id' => $user->id])->count()) {
            if ($user = static::addHearts($user->id, $animalCount * self::HEARTS_COUNT)) {
                return $user;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
