<?php

namespace app\models;

class OrderDetails extends \app\models\base\OrderDetailsBase
{
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['quantity', 'price'], 'required'],
            ]
        );
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['name'] = function () {
            return $this->product->name;
        };

        return $fields;
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {

            $this->calculateTotal();

            return true;
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->calculateTotal();
            return true;
        }
    }

    public function calculateTotal()
    {
        $this->total = $this->price * $this->quantity;
        $this->total = round($this->total, 4);
    }
}
