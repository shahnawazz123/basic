<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "area".
 *
 * @property int $area_id
 * @property int $state_id
 * @property string $name_en
 * @property string $name_ar
 * @property string|null $latitude
 * @property string|null $longitude
 * @property int|null $is_active
 * @property int|null $is_deleted
 *
 * @property State $state
 * @property Block[] $blocks
 * @property Clinics[] $clinics
 * @property PharmacyLocations[] $pharmacyLocations
 */
class Area extends \yii\db\ActiveRecord
{
    public $country_id;
 
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['state_id', 'name_en', 'name_ar'], 'required'],
            [['state_id', 'is_active', 'is_deleted'], 'integer'],
            [['name_en', 'name_ar'], 'string', 'max' => 255],
            [['latitude', 'longitude'], 'string', 'max' => 50],
            [['state_id'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['state_id' => 'state_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'area_id' => 'Area ID',
            'state_id' => 'State',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * Gets query for [[State]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['state_id' => 'state_id']);
    }

    /**
     * Gets query for [[Blocks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlocks()
    {
        return $this->hasMany(Block::className(), ['area_id' => 'area_id']);
    }

    /**
     * Gets query for [[Clinics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClinics()
    {
        return $this->hasMany(Clinics::className(), ['area_id' => 'area_id']);
    }

    /**
     * Gets query for [[PharmacyLocations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacyLocations()
    {
        return $this->hasMany(PharmacyLocations::className(), ['area_id' => 'area_id']);
    }
}
