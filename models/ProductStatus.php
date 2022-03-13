<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_status".
 *
 * @property int $product_status_id
 * @property string $status_name_en
 * @property string $status_name_ar
 *
 * @property ProductStatusHistory[] $productStatusHistories
 */
class ProductStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status_name_en', 'status_name_ar'], 'required'],
            [['status_name_en', 'status_name_ar'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_status_id' => 'Product Status ID',
            'status_name_en' => 'Status Name En',
            'status_name_ar' => 'Status Name Ar',
        ];
    }

    /**
     * Gets query for [[ProductStatusHistories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductStatusHistories()
    {
        return $this->hasMany(ProductStatusHistory::className(), ['product_status_id' => 'product_status_id']);
    }
}
