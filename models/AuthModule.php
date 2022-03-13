<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth_module".
 *
 * @property int $auth_module_id
 * @property string $auth_module_name
 * @property string $auth_module_url
 * @property int $is_active
 * @property int|null $sort_order
 *
 * @property AuthItem[] $authItems
 */
class AuthModule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_module';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_module_name', 'auth_module_url'], 'required'],
            [['is_active', 'sort_order'], 'integer'],
            [['auth_module_name'], 'string', 'max' => 64],
            [['auth_module_url'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'auth_module_id' => 'Auth Module ID',
            'auth_module_name' => 'Auth Module Name',
            'auth_module_url' => 'Auth Module Url',
            'is_active' => 'Is Active',
            'sort_order' => 'Sort Order',
        ];
    }

    /**
     * Gets query for [[AuthItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItems()
    {
        return $this->hasMany(AuthItem::className(), ['auth_module_id' => 'auth_module_id']);
    }
}
