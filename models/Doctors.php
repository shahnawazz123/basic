<?php

namespace app\models;

use Yii;
use himiklab\sortablegrid\SortableGridBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "doctors".
 *
 * @property int $doctor_id
 * @property string $name_en
 * @property string $name_ar
 * @property string $email
 * @property string $password
 * @property int $years_experience
 * @property string $qualification
 * @property string|null $image
 * @property string $gender
 * @property string $accepted_payment_method
 * @property string $registration_number
 * @property string $type V = Video consultation, I = In person consultation
 * @property int|null $consultation_time_online
 * @property int|null $consultation_time_offline
 * @property int|null $clinic_id
 * @property int|null $sort_order
 * @property float|null $consultation_price_regular
 * @property float|null $consultation_price_final
 * @property int $is_featured
 * @property int $is_active
 * @property int $is_deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property DoctorAppointments[] $doctorAppointments
 * @property DoctorCategories[] $doctorCategories
 * @property DoctorInsurances[] $doctorInsurances
 * @property DoctorSymptoms[] $doctorSymptoms
 * @property DoctorWorkingDays[] $doctorWorkingDays
 * @property Clinics $clinic
 * @property PromotionDoctors[] $promotionDoctors
 * @property StoreDoctors[] $storeDoctors
 */
class Doctors extends \yii\db\ActiveRecord
{
    public $password_hash;
    public $insurance_id;
    public $category_id;
    public $symptom_id;
    public $days;
    public $start_time;
    public $end_time;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doctors';
    }

    public function behaviors()
    {
        return [
            'sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sort_order'

            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar', 'email', 'years_experience', 'qualification', 'gender', 'type', 'consultation_price_regular', 'consultation_price_final'], 'required'],
            [['years_experience', 'consultation_time_online', 'consultation_time_offline', 'clinic_id', 'is_active', 'is_deleted', 'is_featured', 'sort_order'], 'integer'],
            [['years_experience', 'consultation_price_regular', 'consultation_price_final', 'consultation_time_online', 'consultation_time_offline'], 'match', 'pattern' => '/^[0-9]+$/', 'message' => "Must be an I    nteger."],
            [['qualification', 'gender', 'registration_number', 'description_ar', 'description_en'], 'string'],
            [['consultation_price_regular', 'consultation_price_final'], 'number'],
            [['created_at', 'type'], 'safe'],
            [['name_en', 'name_ar', 'image'], 'string', 'max' => 200],
            [['email', 'password'], 'string', 'max' => 250],
            ['email', 'email','message' => 'Invalid email address.'],
            [['email'], 'unique', 'message' => 'Email already exist. Please try another one.'],
            ['accepted_payment_method', 'required'],
            [['consultation_price_final'], 'compare', 'compareAttribute' => "consultation_price_regular", 'operator' => '<=', 'type' => 'number'],
            ['password_hash', 'required', 'on' => 'create'],
            [['password_hash'],  'match', 'pattern' => '/^.*(?=^.{6,15}$).*$/', 'message' => 'Password must contain min 6 char.'],
            [['updated_at'], 'string', 'max' => 100],
            [['clinic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clinics::className(), 'targetAttribute' => ['clinic_id' => 'clinic_id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'doctor_id' => 'Doctor ID',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'email' => 'Email',
            'password' => 'Password',
            'password_hash' => 'Password',
            'years_experience' => 'Years Experience',
            'qualification' => 'Qualification',
            'image' => 'Image',
            'gender' => 'Gender',
            'type' => 'Type',
            'consultation_time_online' => 'Consultation Time Online (in minutes)',
            'consultation_time_offline' => 'Consultation Time Offline (in minutes)',
            'clinic_id' => 'Clinic/Hospitals',
            'consultation_price_regular' => 'Regular price for Consultation',
            'consultation_price_final' => 'Final price for Consultation',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'insurance_id' => 'Insurance',
            'category_id' => 'Category',
            'symptom_id' => 'Symptoms',
            'description_ar' => 'Description in Arabic',
            'description_en' => 'Description in English',
            'registration_number' => 'Registration/Certificate Number',
            'days' => '',
            'accepted_payment_method' => 'Accepted Payment Method',
        ];
    }

    /**
     * Gets query for [[DoctorAppointments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorAppointments()
    {
        return $this->hasMany(DoctorAppointments::className(), ['doctor_id' => 'doctor_id']);
    }

    /**
     * Gets query for [[DoctorCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorCategories()
    {
        return $this->hasMany(DoctorCategories::className(), ['doctor_id' => 'doctor_id']);
    }

    /**
     * Gets query for [[DoctorInsurances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorInsurances()
    {
        return $this->hasMany(DoctorInsurances::className(), ['doctor_id' => 'doctor_id']);
    }

    /**
     * Gets query for [[DoctorSymptoms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorSymptoms()
    {
        return $this->hasMany(DoctorSymptoms::className(), ['doctor_id' => 'doctor_id']);
    }

    /**
     * Gets query for [[DoctorWorkingDays]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorWorkingDays()
    {
        return $this->hasMany(DoctorWorkingDays::className(), ['doctor_id' => 'doctor_id']);
    }

    /**
     * Gets query for [[Clinic]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClinic()
    {
        return $this->hasOne(Clinics::className(), ['clinic_id' => 'clinic_id']);
    }

    /**
     * Gets query for [[PromotionDoctors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionDoctors()
    {
        return $this->hasMany(PromotionDoctors::className(), ['doctor_id' => 'doctor_id']);
    }

    /**
     * Gets query for [[StoreDoctors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStoreDoctors()
    {
        return $this->hasMany(StoreDoctors::className(), ['doctor_id' => 'doctor_id']);
    }
}
