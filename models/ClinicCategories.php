<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clinic_categories".
 *
 * @property int $clinic_category_id
 * @property int $clinic_id
 * @property int $category_id
 *
 * @property Clinics $clinic
 * @property Category $category
 */
class ClinicCategories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clinic_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clinic_id', 'category_id'], 'required'],
            [['clinic_id', 'category_id'], 'integer'],
            [['clinic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clinics::className(), 'targetAttribute' => ['clinic_id' => 'clinic_id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'category_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'clinic_category_id' => 'Clinic Category ID',
            'clinic_id' => 'Clinic ID',
            'category_id' => 'Category ID',
        ];
    }

    /**
     * Gets query for [[Clinic]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClinic()
    {
        return $this->hasOne(Clinics::className(), ['clinic_id' => 'clinic_id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['category_id' => 'category_id']);
    }
}
