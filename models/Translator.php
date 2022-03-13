<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "translator".
 *
 * @property int $translator_id
 * @property string $name_en
 * @property string $name_ar
 * @property string $email
 * @property string $password
 * @property int $is_active
 * @property int $is_deleted
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Translator extends \yii\db\ActiveRecord
{
    public $password_hash;
  
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'translator';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar', 'email', 'password'], 'required'],
            ['password_hash', 'required', 'on' => 'create'],
            [['password_hash'],  'match', 'pattern' => '/^.*(?=^.{6,15}$).*$/', 'message' => 'Password must contain min 6 char.'],
            [['is_active', 'is_deleted'], 'integer'],
            [['email', 'password'], 'string', 'max' => 250],
            ['email', 'email'],
            [['email'], 'unique', 'message' => 'Email already exist. Please try another one.'],
            [['created_at', 'updated_at'], 'safe'],
            [['name_en', 'name_ar', 'email', 'password'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'translator_id' => 'Translator ID',
            'name_en' => 'Name In English',
            'name_ar' => 'Name In Arabic',
            'email' => 'Email',
            'password' => 'Password',
            'password_hash' => 'Password',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    
    /**
     * Gets All the translator appointments
     *
     * @return array translator appointment list
     */
    public function getAppointments()
    {
        return $this->hasMany(DoctorAppointments::className(), ['translator_id'=>'translator_id']);
    }
}
