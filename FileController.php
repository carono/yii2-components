<?php

namespace carono\components;


use yii\web\HttpException;

class FileController extends \yii\web\Controller
{
    protected function out($path, $name = null, $options = [])
    {
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if ($this->beforeSend($path, $name)) {
                \Yii::$app->response->sendContentAsFile($content, $name, $options);
            } else {
                throw new HttpException(500, 'Download is interrupted');
            }
            $this->afterSend($path, $name);
        } else {
            throw new HttpException(404, 'File not found');
        }
    }

    public function beforeSend($path, $name)
    {
        return true;
    }

    public function afterSend($path, $name)
    {
    }
}