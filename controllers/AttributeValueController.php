<?php
/**
 * Created by PhpStorm.
 * User: chirag
 * Date: 6/26/2018
 * Time: 10:27 AM
 */

namespace app\controllers;

use Yii;
use himiklab\sortablegrid\SortableGridAction;
use yii\web\Controller;
use app\models\AttributeValues;

class AttributeValueController extends Controller
{

    public function actions()
    {
        return [
            'sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => AttributeValues::className(),
            ],
        ];
    }

}