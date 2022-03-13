<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "store_pharmacies".
 *
 * @property int $store_pharmacy_id
 * @property int $store_id
 * @property int $pharmacy_id
 *
 * @property Pharmacies $pharmacy
 * @property Stores $store
 */
class StorePharmacies extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'store_pharmacies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'pharmacy_id'], 'required'],
            [['store_id', 'pharmacy_id'], 'integer'],
            [['pharmacy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pharmacies::className(), 'targetAttribute' => ['pharmacy_id' => 'pharmacy_id']],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::className(), 'targetAttribute' => ['store_id' => 'store_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'store_pharmacy_id' => 'Store Pharmacy ID',
            'store_id' => 'Store ID',
            'pharmacy_id' => 'Pharmacy ID',
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
     * Gets query for [[Store]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::className(), ['store_id' => 'store_id']);
    }
}
