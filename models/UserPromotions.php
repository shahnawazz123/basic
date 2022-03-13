<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_promotions".
 *
 * @property int $user_promotion_id
 * @property int $user_id
 * @property int $promotion_id
 * @property int $status
 *
 * @property Promotions $promotion
 * @property Users $user
 */
class UserPromotions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_promotions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'promotion_id'], 'required'],
            [['user_id', 'promotion_id', 'status'], 'integer'],
            [['promotion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Promotions::className(), 'targetAttribute' => ['promotion_id' => 'promotion_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_promotion_id' => 'User Promotion ID',
            'user_id' => 'User ID',
            'promotion_id' => 'Promotion ID',
            'status' => 'Status',
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }
}
