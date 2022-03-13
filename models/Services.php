<?php

namespace app\models;

use Yii;
use himiklab\sortablegrid\SortableGridBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "services".
 *
 * @property int $service_id
 * @property string $name_en
 * @property string $name_ar
 * @property string|null $image_en
 * @property string|null $image_ar
 * @property int|null $sort_order
 * @property int $is_active
 * @property int $is_deleted
 *
 * @property LabServices[] $labServices
 */
class Services extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'services';
    }

    public function behaviors()
    {
        return [
            'sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sort_order'

            ], 
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar'], 'required'],
            [['is_active', 'is_deleted'], 'integer'],
            [['name_en', 'name_ar', 'image_en', 'image_ar'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'service_id' => 'Service ID',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'image_en' => 'Image in English',
            'image_ar' => 'Image in Arabic',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * Gets query for [[LabServices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabServices()
    {
        return $this->hasMany(LabServices::className(), ['service_id' => 'service_id']);
    }
}
