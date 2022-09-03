<?php

namespace app\modules\api\controllers\v1;

class ProductsController extends \app\components\RestController
{
    public $modelClass = 'app\models\Products';
    public $endPoint = __CLASS__;
    public $enableCsrfValidation = false;
}
