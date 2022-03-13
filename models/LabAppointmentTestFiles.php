<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lab_appointment_test_files".
 *
 * @property int $lab_appointment_test_file_id
 * @property int $lab_appointment_test_id
 * @property string $file
 * @property string $created_at
 *
 * @property DoctorAppointmentTestResults[] $doctorAppointmentTestResults
 * @property LabAppointmentTests $labAppointmentTest
 */
class LabAppointmentTestFiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lab_appointment_test_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lab_appointment_test_id', 'file', 'created_at'], 'required'],
            [['lab_appointment_test_id'], 'integer'],
            [['created_at'], 'safe'],
            [['file'], 'string', 'max' => 100],
            [['lab_appointment_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => LabAppointmentTests::className(), 'targetAttribute' => ['lab_appointment_test_id' => 'lab_appointment_test_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lab_appointment_test_file_id' => 'Lab Appointment Test File ID',
            'lab_appointment_test_id' => 'Lab Appointment Test ID',
            'file' => 'File',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[DoctorAppointmentTestResults]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorAppointmentTestResults()
    {
        return $this->hasMany(DoctorAppointmentTestResults::className(), ['lab_appointment_test_file_id' => 'lab_appointment_test_file_id']);
    }

    /**
     * Gets query for [[LabAppointmentTest]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabAppointmentTest()
    {
        return $this->hasOne(LabAppointmentTests::className(), ['lab_appointment_test_id' => 'lab_appointment_test_id']);
    }
}
