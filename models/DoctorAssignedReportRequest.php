<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_assigned_report_request".
 *
 * @property int $request_id
 * @property int $doctor_report_request_id
 * @property int $report_id
 * @property int $is_approved
 * 
 * @property UserReport $userReport
 */
class DoctorAssignedReportRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doctor_assigned_report_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doctor_report_request_id', 'report_id'], 'required'],
            [['doctor_report_request_id', 'report_id', 'is_approved'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'request_id' => 'Request ID',
            'doctor_report_request_id' => 'Doctor Report Request ID',
            'report_id' => 'Report ID',
            'is_approved' => 'Is Approved',
        ];
    }

    public function getUserReport()
    {
        return $this->hasOne(UserReport::className(), ['report_id' => 'report_id']);
    }
}
