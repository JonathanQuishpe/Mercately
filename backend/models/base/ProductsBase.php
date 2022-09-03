<?php

namespace app\models\base;

use Yii;
use app\models\OrderDetails;

/**
 * This is the model class for table "products".
*
    * @property integer $id
    * @property string $name
    * @property string $price
    *
            * @property OrderDetails[] $orderDetails
    */
class ProductsBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'products';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['price'], 'number'],
            [['name'], 'string', 'max' => 45],
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => Yii::t('app', 'ID'),
    'name' => Yii::t('app', 'Name'),
    'price' => Yii::t('app', 'Price'),
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getOrderDetails()
    {
    return $this->hasMany(OrderDetails::className(), ['product_id' => 'id']);
    }
}