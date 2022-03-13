<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_reports_images".
 *
 * @property int $user_reports_image_id
 * @property int $report_id
 * @property string|null $image
 *
 * @property UserReport $report
 */
class UserReportsImages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_reports_images';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['report_id'], 'required'],
            [['report_id'], 'integer'],
            [['image'], 'string', 'max' => 100],
            [['report_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserReport::className(), 'targetAttribute' => ['report_id' => 'report_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_reports_image_id' => 'User Reports Image ID',
            'report_id' => 'Report ID',
            'image' => 'Image',
        ];
    }

    /**
     * Gets query for [[Report]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReport()
    {
        return $this->hasOne(UserReport::className(), ['report_id' => 'report_id']);
    }
}
