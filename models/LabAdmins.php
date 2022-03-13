<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lab_admins".
 *
 * @property int $lab_admin_id
 * @property string $name_en
 * @property string $name_ar
 * @property int $lab_id
 * @property string $email
 * @property string $password
 * @property int $is_active
 * @property int $is_deleted
 *
 * @property Labs $lab
 */
class LabAdmins extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $password_hash;
    public static function tableName()
    {
        return 'lab_admins';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar', 'lab_id', 'email'], 'required'],
            [['lab_id', 'is_active', 'is_deleted'], 'integer'],
            [['name_en', 'name_ar', 'email'], 'string', 'max' => 100],
            ['password_hash', 'required', 'on' => 'create'],
            [['password_hash'],  'match', 'pattern' => '/^.*(?=^.{6,15}$).*$/', 'message' => 'Password must contain min 6 char.'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            [['email'], 'unique', 'message' => 'Email already exist. Please try another one.'],
            [['lab_id'], 'exist', 'skipOnError' => true, 'targetClass' => Labs::className(), 'targetAttribute' => ['lab_id' => 'lab_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lab_admin_id' => 'Lab Admin',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'lab_id' => 'Lab',
            'email' => 'Email',
            'password' => 'Password',
            'password_hash' => 'Password',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * Gets query for [[Lab]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLab()
    {
        return $this->hasOne(Labs::className(), ['lab_id' => 'lab_id']);
    }
}
