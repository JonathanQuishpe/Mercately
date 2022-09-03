<?php

namespace app\models\base;

use Yii;
use app\models\OrderDetails;

/**
 * This is the model class for table "orders".
*
    * @property integer $id
    * @property string $sku
    * @property string $total
    * @property string $date
    * @property integer $status
    *
            * @property OrderDetails[] $orderDetails
    */
class OrdersBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'orders';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['total'], 'number'],
            [['date'], 'safe'],
            [['status'], 'integer'],
            [['sku'], 'string', 'max' => 45],
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => Yii::t('app', 'ID'),
    'sku' => Yii::t('app', 'Sku'),
    'total' => Yii::t('app', 'Total'),
    'date' => Yii::t('app', 'Date'),
    'status' => Yii::t('app', 'Status'),
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getOrderDetails()
    {
    return $this->hasMany(OrderDetails::className(), ['order_id' => 'id']);
    }
}