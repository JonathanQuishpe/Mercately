<?php

namespace app\models;

use yii\base\Model;

class Util extends Model
{
  public static function validatePostData($postContent)
  {
    $data_json = null;
    $data = [];
    if (isset($postContent)) {
      $data_json = json_decode($postContent, true);
    }
    if (!is_null($data_json)) { //Se envio en formato JSON
      $data = $data_json;
    } else { //Se envia form
      $data = $_POST;
    }
    return $data;
  }

  public static function generateRandomString($length = 10)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
  }
}
