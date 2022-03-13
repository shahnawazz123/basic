<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tests".
 *
 * @property int $test_id
 * @property string $name_en
 * @property string $name_ar
 * @property float $price
 * @property int $is_home_service
 * @property int $is_active
 * @property int $is_deleted
 *
 * @property DoctorAppointmentRequests[] $doctorAppointmentRequests
 * @property LabAppointmentTests[] $labAppointmentTests
 * @property LabTests[] $labTests
 * @property TestCategories[] $testCategories
 */
class Tests extends \yii\db\ActiveRecord
{
    public $category_id;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar', 'price'], 'required'],
            [['price'], 'number'],
            [['price'], 'match', 'pattern' => '/^[0-9]+$/',"message"=>"Must not be negative"],
            [['is_home_service', 'is_active', 'is_deleted'], 'integer'],
            [['name_en', 'name_ar'], 'string', 'max' => 100],
            [['price'], 'match', 'pattern' => '/^[0-9]+$/'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'test_id' => 'Test ID',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'price' => 'Price',
            'is_home_service' => 'Is Home Service',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'category_id' => 'Category',
        ];
    }

    /**
     * Gets query for [[DoctorAppointmentRequests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorAppointmentRequests()
    {
        return $this->hasMany(DoctorAppointmentRequests::className(), ['test_id' => 'test_id']);
    }

    /**
     * Gets query for [[LabAppointmentTests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabAppointmentTests()
    {
        return $this->hasMany(LabAppointmentTests::className(), ['test_id' => 'test_id']);
    }

    /**
     * Gets query for [[LabTests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabTests()
    {
        return $this->hasMany(LabTests::className(), ['test_id' => 'test_id']);
    }

    /**
     * Gets query for [[TestCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestCategories()
    {
        return $this->hasMany(TestCategories::className(), ['test_id' => 'test_id']);
    }
}
