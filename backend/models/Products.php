<?php

namespace app\models;

class Products extends \app\models\base\ProductsBase
{
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['name', 'price'], 'required'],
            ]
        );
    }
}
