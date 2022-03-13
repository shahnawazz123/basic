<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lab_appointment_tests".
 *
 * @property int $lab_appointment_test_id
 * @property int $lab_appointment_id
 * @property int $test_id
 *
 * @property LabAppointmentTestFiles[] $labAppointmentTestFiles
 * @property LabAppointments $labAppointment
 * @property Tests $test
 */
class LabAppointmentTests extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lab_appointment_tests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lab_appointment_id', 'test_id'], 'required'],
            [['lab_appointment_id', 'test_id'], 'integer'],
            [['lab_appointment_id'], 'exist', 'skipOnError' => true, 'targetClass' => LabAppointments::className(), 'targetAttribute' => ['lab_appointment_id' => 'lab_appointment_id']],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tests::className(), 'targetAttribute' => ['test_id' => 'test_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lab_appointment_test_id' => 'Lab Appointment Test ID',
            'lab_appointment_id' => 'Lab Appointment ID',
            'test_id' => 'Test ID',
        ];
    }

    /**
     * Gets query for [[LabAppointmentTestFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabAppointmentTestFiles()
    {
        return $this->hasMany(LabAppointmentTestFiles::className(), ['lab_appointment_test_id' => 'lab_appointment_test_id']);
    }

    /**
     * Gets query for [[LabAppointment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabAppointment()
    {
        return $this->hasOne(LabAppointments::className(), ['lab_appointment_id' => 'lab_appointment_id']);
    }

    /**
     * Gets query for [[Test]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(Tests::className(), ['test_id' => 'test_id']);
    }
}
