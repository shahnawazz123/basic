<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "store_labs".
 *
 * @property int $store_lab_id
 * @property int $store_id
 * @property int $lab_id
 *
 * @property Stores $store
 * @property Labs $lab
 */
class StoreLabs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'store_labs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'lab_id'], 'required'],
            [['store_id', 'lab_id'], 'integer'],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::className(), 'targetAttribute' => ['store_id' => 'store_id']],
            [['lab_id'], 'exist', 'skipOnError' => true, 'targetClass' => Labs::className(), 'targetAttribute' => ['lab_id' => 'lab_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'store_lab_id' => 'Store Lab ID',
            'store_id' => 'Store ID',
            'lab_id' => 'Lab ID',
        ];
    }

    /**
     * Gets query for [[Store]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::className(), ['store_id' => 'store_id']);
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
