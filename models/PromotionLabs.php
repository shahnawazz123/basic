<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "promotion_labs".
 *
 * @property int $promotion_lab_id
 * @property int $lab_id
 * @property int $promotion_id
 *
 * @property Promotions $promotion
 * @property Labs $lab
 */
class PromotionLabs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promotion_labs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lab_id', 'promotion_id'], 'required'],
            [['lab_id', 'promotion_id'], 'integer'],
            [['promotion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Promotions::className(), 'targetAttribute' => ['promotion_id' => 'promotion_id']],
            [['lab_id'], 'exist', 'skipOnError' => true, 'targetClass' => Labs::className(), 'targetAttribute' => ['lab_id' => 'lab_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'promotion_lab_id' => 'Promotion Lab ID',
            'lab_id' => 'Lab ID',
            'promotion_id' => 'Promotion ID',
        ];
    }

    /**
     * Gets query for [[Promotion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotion()
    {
        return $this->hasOne(Promotions::className(), ['promotion_id' => 'promotion_id']);
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
}
