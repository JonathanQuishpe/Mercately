<?php

namespace app\modules\api\controllers\v1;

use yii\helpers\Url;
use Yii;
use yii\web\ServerErrorHttpException;
use yii\base\Exception;
use yii\web\NotFoundHttpException;

class OrdersController extends \app\components\RestController
{
    public $modelClass = 'app\models\Orders';
    public $endPoint = __CLASS__;
    public $enableCsrfValidation = false;

    public function actionCreate()
    {
        $model = new $this->modelClass([
            'scenario' => $this->createScenario,
        ]);

        $postContent = file_get_contents("php://input");
        $data = \app\models\Util::validatePostData($postContent);

        $model->load($data, '');
        $model->sku = \app\models\Util::generateRandomString(10);
        $transaction = Yii::$app->db->beginTransaction();
        $flag = false;
        try {
            if ($model->save() && $model->validate()) {
                $flag = true;

                foreach ($data['order_details'] as $item) {
                    $itemObj = new \app\models\OrderDetails();
                    $itemObj->scenario = $this->createScenario;
                    $itemObj->attributes = $item;
                    $itemObj->order_id = $model->id;
                    $itemObj->validate();
                    if (!($flag = $itemObj->save(false))) {
                        $transaction->rollBack();
                        break;
                    }
                }

                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
                $id = implode(',', array_values($model->getPrimaryKey(true)));
                $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
            } elseif (!$model->hasErrors()) {
                throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
            }

            if ($flag) {
                $transaction->commit();
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }


        return $model;
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!is_object($model)) {
            throw new NotFoundHttpException(Yii::t("app", "Object not found: $id"));
        }

        $model->status = 0;
        $model->save(false);
        return $model;
    }
}
