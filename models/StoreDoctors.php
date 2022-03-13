<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "store_doctors".
 *
 * @property int $store_doctor_id
 * @property int $store_id
 * @property int $doctor_id
 *
 * @property Stores $store
 * @property Doctors $doctor
 */
class StoreDoctors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'store_doctors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'doctor_id'], 'required'],
            [['store_id', 'doctor_id'], 'integer'],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::className(), 'targetAttribute' => ['store_id' => 'store_id']],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctors::className(), 'targetAttribute' => ['doctor_id' => 'doctor_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'store_doctor_id' => 'Store Doctor ID',
            'store_id' => 'Store ID',
            'doctor_id' => 'Doctor ID',
        ];
    }

    /**
     * Gets query for [[Store]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::className(), ['store_id' => 'store_id']);
    }

    /**
     * Gets query for [[Doctor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctors::className(), ['doctor_id' => 'doctor_id']);
    }
}
