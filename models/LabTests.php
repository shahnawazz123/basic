<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lab_tests".
 *
 * @property int $lab_test_id
 * @property int $test_id
 * @property int $lab_id
 *
 * @property Labs $lab
 * @property Tests $test
 */
class LabTests extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lab_tests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['test_id', 'lab_id'], 'required'],
            [['test_id', 'lab_id'], 'integer'],
            [['lab_id'], 'exist', 'skipOnError' => true, 'targetClass' => Labs::className(), 'targetAttribute' => ['lab_id' => 'lab_id']],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tests::className(), 'targetAttribute' => ['test_id' => 'test_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lab_test_id' => 'Lab Test ID',
            'test_id' => 'Test ID',
            'lab_id' => 'Lab ID',
        ];
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

    /**
     * Gets query for [[Test]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(Tests::className(), ['test_id' => 'test_id']);
    }
}
