<?php
/**
 * Created by PhpStorm.
 * User: chirag
 * Date: 7/9/2018
 * Time: 9:46 AM
 */

namespace app\controllers;

use Yii;
use app\models\ProductImages;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use himiklab\sortablegrid\SortableGridAction;

class ProductImageController extends Controller
{
    public function actions() {
        return [
            'sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => ProductImages::className(),
            ],
        ];
    }
}