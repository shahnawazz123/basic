<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_report".
 *
 * @property int $report_id
 * @property int $user_id
 * @property string $title
 * @property int $is_deleted
 * @property string $created_at
 *
 * @property Users $user
 * @property UserReportsImages[] $userReportsImages
 */
class UserReport extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_report';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'title'], 'required'],
            [['user_id', 'is_deleted'], 'integer'],
            [['created_at'], 'safe'],
            [['title'], 'string', 'max' => 100],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'report_id' => 'Report ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
        ];
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

    /**
     * Gets query for [[UserReportsImages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserReportsImages()
    {
        return $this->hasMany(UserReportsImages::className(), ['report_id' => 'report_id']);
    }
}
