<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lab_services".
 *
 * @property int $lab_service_id
 * @property int $lab_id
 * @property int $service_id
 *
 * @property Services $service
 * @property Labs $lab
 */
class LabServices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lab_services';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lab_id', 'service_id'], 'required'],
            [['lab_id', 'service_id'], 'integer'],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Services::className(), 'targetAttribute' => ['service_id' => 'service_id']],
            [['lab_id'], 'exist', 'skipOnError' => true, 'targetClass' => Labs::className(), 'targetAttribute' => ['lab_id' => 'lab_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lab_service_id' => 'Lab Service ID',
            'lab_id' => 'Lab ID',
            'service_id' => 'Service ID',
        ];
    }

    /**
     * Gets query for [[Service]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Services::className(), ['service_id' => 'service_id']);
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
