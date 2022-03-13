<?php

namespace app\controllers;

use Yii;
use yii\web\UploadedFile;

class UploadController extends \yii\web\Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionAdminImage()
    {
        $imageFile = UploadedFile::getInstanceByName('Admin[image]');
        //$directory = \Yii::getAlias('@app/web/uploads') . DIRECTORY_SEPARATOR . Yii::$app->session->id . DIRECTORY_SEPARATOR;
        $directory = \Yii::getAlias('@app/web/uploads') . DIRECTORY_SEPARATOR;
        if ($imageFile) {
            $filetype = mime_content_type($imageFile->tempName);
            $allowed = array('image/png', 'image/jpeg', 'image/gif');
            if (!in_array(strtolower($filetype), $allowed)) {
                return json_encode([
                    'files' => [
                        'error' => "File type not supported",
                    ]
                ]);
            } else {
                $uid = uniqid(time(), true);
                $fileName = $uid . '.' . $imageFile->extension;
                $filePath = $directory . $fileName;
                if ($imageFile->saveAs($filePath)) {
                    $path = \yii\helpers\BaseUrl::home() . 'uploads/' . $fileName;

                    return json_encode([
                        'files' => [
                            'name' => $fileName,
                            'size' => $imageFile->size,
                            "url" => $path,
                            "thumbnailUrl" => $path,
                            "deleteUrl" => 'image-delete?name=' . $fileName,
                            "deleteType" => "POST",
                            'error' => ""
                        ]
                    ]);
                }
            }
        }
        return '';
    }

    public function actionCommon($attribute)
    {
        $imageFile = UploadedFile::getInstanceByName($attribute);
        //$directory = \Yii::getAlias('@app/web/uploads') . DIRECTORY_SEPARATOR . Yii::$app->session->id . DIRECTORY_SEPARATOR;
        $directory = \Yii::getAlias('@app/web/uploads') . DIRECTORY_SEPARATOR;
        if ($imageFile) {
            $filetype = mime_content_type($imageFile->tempName);
            $allowed = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'application/pdf');
            if (!in_array(strtolower($filetype), $allowed)) {
                return json_encode([
                    'files' => [
                        'error' => "File type not supported",
                    ]
                ]);
            } else {
                $uid = uniqid(time(), true);
                $fileName = $uid . '.' . $imageFile->extension;
                $filePath = $directory . $fileName;
                if ($imageFile->saveAs($filePath)) {
                    $path = \yii\helpers\BaseUrl::home() . 'uploads/' . $fileName;

                    return json_encode([
                        'files' => [
                            'name' => $fileName,
                            'size' => $imageFile->size,
                            "url" => $path,
                            "thumbnailUrl" => $path,
                            "deleteUrl" => 'image-delete?name=' . $fileName,
                            "deleteType" => "POST",
                            'error' => ""
                        ]
                    ]);
                }
            }
        }
        return '';
    }

    public function actionProductImage()
    {
        $model = new \app\models\Product;
        $model->images = \yii\web\UploadedFile::getInstance($model, 'images');
        $filetype = mime_content_type($model->images->tempName);
        $allowed = array('image/png', 'image/jpeg', 'image/gif');
        if (!in_array(strtolower($filetype), $allowed)) {
            return json_encode([
                'error' => "File type not supported",
            ]);
        } else {
            $ext = explode(".", $model->images->name);
            $destName = time() . rand() . "." . end($ext);

            $model->images->saveAs('uploads/' . $destName);

            echo json_encode(['status' => 200, 'image' => $destName]);
        }
    }

    public function actionVideo($attribute)
    {
        $videoFile = UploadedFile::getInstanceByName($attribute);
        $directory = \Yii::getAlias('@app/web/uploads') . DIRECTORY_SEPARATOR . Yii::$app->session->id . DIRECTORY_SEPARATOR;
        if ($videoFile) {
            $filetype = mime_content_type($videoFile->tempName);
            $allowed = array('video/mp4');
            if (!in_array(strtolower($filetype), $allowed)) {
                return json_encode([
                    'files' => [
                        'error' => "File type not supported"
                    ]
                ]);
            } else {
                $uid = uniqid(time(), true);
                $fileName = $uid . '.' . $videoFile->extension;
                $filePath = $directory . $fileName;
                if ($videoFile->saveAs($filePath)) {
                    $path = \yii\helpers\BaseUrl::home() . 'uploads/' . Yii::$app->session->id . DIRECTORY_SEPARATOR . $fileName;

                    return json_encode([
                        'files' => [
                            'name' => $fileName,
                            'size' => $videoFile->size,
                            "url" => $path,
                            "thumbnailUrl" => $path,
                            "deleteUrl" => 'image-delete?name=' . $fileName,
                            "deleteType" => "POST",
                            'error' => ""
                        ]
                    ]);
                }
            }
        }
        return '';
    }
}
