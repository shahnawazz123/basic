<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "status".
 *
 * @property int $status_id
 * @property string $name_en
 * @property string|null $name_ar
 * @property string|null $color
 * @property string|null $list_order
 *
 * @property OrderStatus[] $orderStatuses
 */
class Status extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en'], 'required'],
            [['name_en', 'name_ar', 'list_order'], 'string', 'max' => 45],
            [['color'], 'string', 'max' => 7],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'status_id' => 'Status ID',
            'name_en' => 'Name En',
            'name_ar' => 'Name Ar',
            'color' => 'Color',
            'list_order' => 'List Order',
        ];
    }

    /**
     * Gets query for [[OrderStatuses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderStatuses()
    {
        return $this->hasMany(OrderStatus::className(), ['status_id' => 'status_id']);
    }
}
