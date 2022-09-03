<?php

namespace app\models\base;

use Yii;
use app\models\Orders;
use app\models\Products;

/**
 * This is the model class for table "order_details".
*
    * @property integer $id
    * @property integer $quantity
    * @property string $price
    * @property string $total
    * @property integer $order_id
    * @property integer $product_id
    *
            * @property Orders $order
            * @property Products $product
    */
class OrderDetailsBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'order_details';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['quantity', 'order_id', 'product_id'], 'integer'],
            [['price', 'total'], 'number'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => Yii::t('app', 'ID'),
    'quantity' => Yii::t('app', 'Quantity'),
    'price' => Yii::t('app', 'Price'),
    'total' => Yii::t('app', 'Total'),
    'order_id' => Yii::t('app', 'Order ID'),
    'product_id' => Yii::t('app', 'Product ID'),
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getOrder()
    {
    return $this->hasOne(Orders::className(), ['id' => 'order_id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getProduct()
    {
    return $this->hasOne(Products::className(), ['id' => 'product_id']);
    }
}