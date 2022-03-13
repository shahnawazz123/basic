<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lab_appointments".
 *
 * @property int $lab_appointment_id
 * @property string $name
 * @property string|null $email
 * @property string $phone_number
 * @property string $admin_commission
 * @property int|null $lab_id
 * @property string|null $type
 * @property int $is_paid
 * @property string|null $paymode
 * @property float $lab_amount
 * @property string|null $sample_collection_time
 * @property string|null $sample_collection_address
 * @property string|null $prescription_file
 * @property int $is_deleted
 * @property string $created_at
 * @property string $updated_at
 * @property int $user_id
 * @property int|null $kid_id
 *
 * @property LabAppointmentAnswers[] $labAppointmentAnswers
 * @property LabAppointmentTests[] $labAppointmentTests
 * @property Labs $lab
 * @property Users $user
 * @property Kids $kid
 * @property Labs $lab
 */
class LabAppointments extends \yii\db\ActiveRecord
{
    public $country_id;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lab_appointments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'phone_number', 'lab_amount', 'created_at', 'updated_at', 'user_id'], 'required'],
            [['lab_id', 'is_paid', 'is_deleted', 'user_id', 'kid_id'], 'integer'],
            [['type', 'paymode', 'sample_collection_address'], 'string'],
            [['lab_amount',], 'number'],
            [['sample_collection_time', 'created_at', 'updated_at','admin_commission'], 'safe'],
            [['name', 'email', 'phone_number', 'prescription_file'], 'string', 'max' => 100],
            [['lab_id'], 'exist', 'skipOnError' => true, 'targetClass' => Labs::className(), 'targetAttribute' => ['lab_id' => 'lab_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
            [['kid_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kids::className(), 'targetAttribute' => ['kid_id' => 'kid_id']],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lab_appointment_id' => 'Lab Appointment ID',
            'name' => 'Name',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'lab_id' => 'Lab',
            'type' => 'Type',
            'is_paid' => 'Is Paid',
            'paymode' => 'Paymode',
            'lab_amount' => 'Lab Amount',
            'sample_collection_time' => 'Sample Collection Time',
            'sample_collection_address' => 'Sample Collection Address',
            'prescription_file' => 'Prescription File',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_id' => 'User',
            'kid_id' => 'Appointment Booked for',
            'admin_commission' => 'Admin Commission %',
        ];
    }

    /**
     * Gets query for [[LabAppointmentAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabAppointmentAnswers()
    {
        return $this->hasMany(LabAppointmentAnswers::className(), ['lab_appointment_id' => 'lab_appointment_id']);
    }

    /**
     * Gets query for [[LabAppointmentTests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabAppointmentTests()
    {
        return $this->hasMany(LabAppointmentTests::className(), ['lab_appointment_id' => 'lab_appointment_id']);
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
     * Gets query for [[Kid]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKid()
    {
        return $this->hasOne(Kids::className(), ['kid_id' => 'kid_id']);
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['type_id' => 'lab_appointment_id']);
    }
}
