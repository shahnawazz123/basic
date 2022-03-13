<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "kids".
 *
 * @property int $kid_id
 * @property string $name_en
 * @property string $name_ar
 * @property string $civil_id
 * @property string $dob
 * @property int $is_deleted
 * @property string $created_at
 * @property string $updated_at
 * @property int $user_id
 *
 * @property DoctorAppointments[] $doctorAppointments
 * @property Users $user
 * @property LabAppointments[] $labAppointments
 */
class Kids extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kids';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar', 'civil_id', 'dob', 'user_id'], 'required'],
            [['is_deleted', 'user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name_en', 'name_ar', 'civil_id', 'dob'], 'string', 'max' => 100],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kid_id' => 'Kid ID',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'civil_id' => 'Civil ID',
            'dob' => 'Dob',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_id' => 'User',
        ];
    }

    /**
     * Gets query for [[DoctorAppointments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorAppointments()
    {
        return $this->hasMany(DoctorAppointments::className(), ['kid_id' => 'kid_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[LabAppointments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabAppointments()
    {
        return $this->hasMany(LabAppointments::className(), ['kid_id' => 'kid_id']);
    }
}
