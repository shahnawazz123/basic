<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cms".
 *
 * @property int $cms_id
 * @property string $title_en
 * @property string $title_ar
 * @property string $content_en
 * @property string $content_ar
 * @property int $is_deleted
 */
class Cms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title_en', 'title_ar', 'content_en', 'content_ar'], 'required'],
            [['content_en', 'content_ar'], 'string'],
            [['is_deleted'], 'integer'],
            [['title_en', 'title_ar'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cms_id' => 'Cms ID',
            'title_en' => 'Title in English',
            'title_ar' => 'Title in Arabic',
            'content_en' => 'Content in English',
            'content_ar' => 'Content in Arabic',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
