<?php

namespace app\models;

class Orders extends \app\models\base\OrdersBase
{
    public $order_details;

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'order_details'
                    ], 'required',
                ],
            ]
        );
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['order_details'] = function () {
            return $this->orderDetails;
        };

        return $fields;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->calculateOrder();
            $this->date = date('Y-m-d');
            return true;
        }
    }

    public function calculateOrder()
    {
        if (!$this->order_details) {
            return;
        }

        foreach ($this->order_details as $item) {
            $itemObj = new \app\models\OrderDetails();
            $itemObj->scenario = $this->scenario;
            $itemObj->attributes = $item;
            $itemObj->validate();

            $this->total += $itemObj->total;
        }
    }
}
