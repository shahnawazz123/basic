<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "state".
 *
 * @property int $state_id
 * @property int $country_id
 * @property string $name_en
 * @property string $name_ar
 * @property int $is_active
 * @property int $is_deleted
 *
 * @property Area[] $areas
 * @property Clinics[] $clinics
 * @property PharmacyLocations[] $pharmacyLocations
 * @property Country $country
 */
class State extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'state';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'name_en', 'name_ar'], 'required'],
            [['country_id', 'is_active', 'is_deleted'], 'integer'],
            [['name_en', 'name_ar'], 'string', 'max' => 255],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'country_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'state_id' => 'State ID',
            'country_id' => 'Country',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * Gets query for [[Areas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAreas()
    {
        return $this->hasMany(Area::className(), ['state_id' => 'state_id']);
    }

    /**
     * Gets query for [[Clinics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClinics()
    {
        return $this->hasMany(Clinics::className(), ['governorate_id' => 'state_id']);
    }

    /**
     * Gets query for [[PharmacyLocations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacyLocations()
    {
        return $this->hasMany(PharmacyLocations::className(), ['governorate_id' => 'state_id']);
    }

    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['country_id' => 'country_id']);
    }
}
