<?php

namespace app\models;

use yii\base\Model;

class ExcelUpload extends Model
{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'required', 'message' => \Yii::t('app', 'Upload at least one file.')],
            [['file'], 'file','extensions' => 'xlsx,xls'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => \Yii::t('app', 'Select excel file'),
        ];
    }

}