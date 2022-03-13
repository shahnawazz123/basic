<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "block".
 *
 * @property int $block_id
 * @property string $name_en
 * @property string $name_ar
 * @property int $area_id
 *
 * @property Area $area
 */
class Block extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'block';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar', 'area_id'], 'required'],
            [['area_id'], 'integer'],
            [['name_en', 'name_ar'], 'string', 'max' => 100],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::className(), 'targetAttribute' => ['area_id' => 'area_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'block_id' => 'Block ID',
            'name_en' => 'Name En',
            'name_ar' => 'Name Ar',
            'area_id' => 'Area ID',
        ];
    }

    /**
     * Gets query for [[Area]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Area::className(), ['area_id' => 'area_id']);
    }
}
