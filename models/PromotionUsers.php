<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "promotion_users".
 *
 * @property int $promotion_target_user_id
 * @property int $promotion_id
 * @property int $user_id
 * @property string $datetime
 *
 * @property Promotions $promotion
 * @property Users $user
 */
class PromotionUsers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promotion_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['promotion_id', 'user_id', 'datetime'], 'required'],
            [['promotion_id', 'user_id'], 'integer'],
            [['datetime'], 'safe'],
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
            'promotion_target_user_id' => 'Promotion Target User ID',
            'promotion_id' => 'Promotion ID',
            'user_id' => 'User ID',
            'datetime' => 'Datetime',
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
