<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "admin".
 *
 * @property int $admin_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $password
 * @property string|null $image
 * @property int $is_active
 * @property int $is_deleted
 * @property string|null $admin_type
 *
 * @property Product[] $products
 */
class Admin extends \yii\db\ActiveRecord
{
    public $password_hash,$confirm_password;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'phone', 'password'], 'required'],
            [['is_active', 'is_deleted'], 'integer'],
            [['admin_type'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['email', 'image'], 'string', 'max' => 50],
            [['phone'], 'string', 'max' => 15],
            [['password'], 'string', 'max' => 100],
            ['email', 'email'],
            ['email', 'unique'],
            [['password_hash','confirm_password'], 'required', 'on' => 'create'],
            [['password_hash'],'string','min'=>6],
            //['password_hash', 'match', 'pattern' => '$\S*(?=\S{6,})(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$', 'message' => 'Password should contain at least one upper case letter, one number and one special character'],
            ['confirm_password', 'compare', 'compareAttribute' => 'password_hash','message' => Yii::t('yii', 'Confirm Password must be equal to "Password"')],
            ['phone', 'match', 'pattern' => '/^[0-9-+]+$/', 'message' => Yii::t('yii', 'Your phone can only contain numeric characters with +/-')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'admin_id' => 'Admin ID',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'password' => 'Password',
            'image' => 'Image',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'admin_type' => 'Admin Type',
            'password_hash' => Yii::t('app', 'Password'),
            'confirm_password' => Yii::t('app', 'Confirm Password'),
        ];
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['admin_id' => 'admin_id']);
    }
}
