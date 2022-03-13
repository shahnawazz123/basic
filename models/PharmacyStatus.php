<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pharmacy_status".
 *
 * @property int $pharmacy_status_id
 * @property string $name_en
 * @property string|null $name_ar
 * @property string|null $color
 *
 * @property PharmacyOrderStatus[] $pharmacyOrderStatuses
 */
class PharmacyStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pharmacy_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pharmacy_status_id', 'name_en'], 'required'],
            [['pharmacy_status_id'], 'integer'],
            [['name_en', 'name_ar'], 'string', 'max' => 45],
            [['color'], 'string', 'max' => 7],
            [['pharmacy_status_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pharmacy_status_id' => 'Pharmacy Status ID',
            'name_en' => 'Name En',
            'name_ar' => 'Name Ar',
            'color' => 'Color',
        ];
    }

    /**
     * Gets query for [[PharmacyOrderStatuses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacyOrderStatuses()
    {
        return $this->hasMany(PharmacyOrderStatus::className(), ['pharmacy_status_id' => 'pharmacy_status_id']);
    }
}
