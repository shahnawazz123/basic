<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "insurances".
 *
 * @property int $insurance_id
 * @property string $name_en
 * @property string $name_ar
 * @property string|null $image
 * @property string $phone
 * @property string|null $address
 * @property int|null $is_active
 * @property int|null $is_deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ClinicInsurances[] $clinicInsurances
 * @property DoctorInsurances[] $doctorInsurances
 * @property LabInsurances[] $labInsurances
 */
class Insurances extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'insurances';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar'], 'required'],
            [['is_active', 'is_deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name_en', 'name_ar', 'image', 'phone', 'address'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'insurance_id' => 'Insurance ID',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'image' => 'Logo',
            'phone' => 'Phone',
            'address' => 'Address',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[ClinicInsurances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClinicInsurances()
    {
        return $this->hasMany(ClinicInsurances::className(), ['insurance_id' => 'insurance_id']);
    }

    /**
     * Gets query for [[DoctorInsurances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorInsurances()
    {
        return $this->hasMany(DoctorInsurances::className(), ['insurance_id' => 'insurance_id']);
    }

    /**
     * Gets query for [[LabInsurances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabInsurances()
    {
        return $this->hasMany(LabInsurances::className(), ['insurance_id' => 'insurance_id']);
    }
}
