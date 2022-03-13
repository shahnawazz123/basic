<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notifications".
 *
 * @property int $notification_id
 * @property string $title
 * @property string $message
 * @property int|null $user_id
 * @property string|null $target
 * @property int|null $target_id
 * @property int|null $is_read
 * @property string $posted_date
 */
class Notifications extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notifications';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'message'], 'required'],
            [['message'], 'string'],
            [['is_read'], 'safe'],
            [['user_id', 'target_id'], 'integer'],
            [['posted_date'], 'safe'],
            [['title'], 'string', 'max' => 250],
            [['target'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'notification_id' => 'Notification ID',
            'title' => 'Title',
            'message' => 'Message',
            'user_id' => 'User ID',
            'target' => 'Target',
            'target_id' => 'Target ID',
            'posted_date' => 'Posted Date',
            'is_read' => 'Is Read?',
        ];
    }
}
