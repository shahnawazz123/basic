<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pharmacy_locations".
 *
 * @property int $pharmacy_location_id
 * @property int $pharmacy_id
 * @property string|null $latlon
 * @property int $governorate_id
 * @property int $area_id
 * @property string|null $block
 * @property string|null $street
 * @property string|null $building
 * @property string $name_en
 * @property string $name_ar
 * @property int $is_deleted
 *
 * @property Pharmacies $pharmacy
 * @property State $governorate
 * @property Area $area
 */
class PharmacyLocations extends \yii\db\ActiveRecord
{
    public $locations;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pharmacy_locations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pharmacy_id', 'governorate_id', 'area_id', 'name_en', 'name_ar'], 'required'],
            [['pharmacy_id', 'governorate_id', 'area_id', 'is_deleted'], 'integer'],
            [['latlon', 'block', 'street', 'building', 'name_en', 'name_ar'], 'string', 'max' => 100],
            [['pharmacy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pharmacies::className(), 'targetAttribute' => ['pharmacy_id' => 'pharmacy_id']],
            [['governorate_id'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['governorate_id' => 'state_id']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::className(), 'targetAttribute' => ['area_id' => 'area_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pharmacy_location_id' => 'Pharmacy Location ',
            'pharmacy_id' => 'Pharmacy ',
            'latlon' => 'Latlon',
            'governorate_id' => 'Governorate ',
            'area_id' => 'Area ',
            'block' => 'Block',
            'street' => 'Street',
            'building' => 'Building',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * Gets query for [[Pharmacy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacy()
    {
        return $this->hasOne(Pharmacies::className(), ['pharmacy_id' => 'pharmacy_id']);
    }

    /**
     * Gets query for [[Governorate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGovernorate()
    {
        return $this->hasOne(State::className(), ['state_id' => 'governorate_id']);
    }

    /**
     * Gets query for [[Area]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Area::className(), ['area_id' => 'area_id']);
    }
}
