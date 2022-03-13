<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth_item".
 *
 * @property int $auth_item_id
 * @property string $auth_item_url
 * @property string $auth_item_name
 * @property string|null $auth_item_description
 * @property int $auth_module_id
 * @property string|null $rule_name
 * @property int $is_active
 * @property string $created_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthModule $authModule
 */
class AuthItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_item_url', 'auth_item_name', 'auth_module_id', 'created_at'], 'required'],
            [['auth_item_description', 'rule_name'], 'string'],
            [['auth_module_id', 'is_active'], 'integer'],
            [['created_at'], 'safe'],
            [['auth_item_url'], 'string', 'max' => 256],
            [['auth_item_name'], 'string', 'max' => 64],
            [['auth_module_id'], 'exist', 'skipOnError' => true, 'targetClass' => AuthModule::className(), 'targetAttribute' => ['auth_module_id' => 'auth_module_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'auth_item_id' => 'Auth Item ID',
            'auth_item_url' => 'Auth Item Url',
            'auth_item_name' => 'Auth Item Name',
            'auth_item_description' => 'Auth Item Description',
            'auth_module_id' => 'Auth Module ID',
            'rule_name' => 'Rule Name',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[AuthAssignments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['auth_item_id' => 'auth_item_id']);
    }

    /**
     * Gets query for [[AuthModule]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthModule()
    {
        return $this->hasOne(AuthModule::className(), ['auth_module_id' => 'auth_module_id']);
    }
}
