<?php

/**
 * @link https://www.abitmedia.cloud
 * @copyright Copyright (c) 2021 Abitmedia
 */

namespace app\components;

use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\Response;
use yii\helpers\Url;
use Yii;


class RestController extends \yii\rest\Controller
{
    public $modelClass;
    public $endPoint;
    public $searchClassModel;
    public $searchMethod = null;
    public $updateScenario = Model::SCENARIO_DEFAULT;
    public $createScenario = Model::SCENARIO_DEFAULT;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->modelClass === null) {
            throw new InvalidConfigException('The "modelClass" property must be set.');
        }
        if ($this->endPoint === null) {
            throw new InvalidConfigException('The "endPoint" property must be set.');
        }
        $this->searchClassModel = $this->modelClass . 'Search';
        \Yii::$app->user->enableSession = false;
        \Yii::$app->user->loginUrl = null;
    }

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
        ];
        $behaviors['contentNegotiator']['formats']['application/json'] = \yii\web\Response::FORMAT_JSON;
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    public function beforeAction($action)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return $this->prepareDataProvider();
    }

    public function prepareDataProvider()
    {
        $searchModel = new $this->searchClassModel();
        $objSearchParams = explode("\\", $this->searchClassModel);
        $params = \Yii::$app->request->queryParams;
        if (isset(\Yii::$app->request->queryParams['id'])) {
            $params['uuid'] = $params['id'];
            unset($params['id']);
        }
        if (is_null($this->searchMethod)) {
            $dataProvider = $searchModel->search([end($objSearchParams) => $params]);
        } else {
            $method = $this->searchMethod;
            if (method_exists($searchModel, $this->searchMethod)) {
                $dataProvider = $searchModel->$method([end($objSearchParams) => $params]);
            } else {
                throw new ServerErrorHttpException(Yii::t("app", "search method $method not found"));
            }
        }

        if (isset($params['start_date']) && isset($params['end_date'])) {
            $start_date = $params['start_date'];
            $end_date = $params['end_date'];
            $search_field = (isset($params['search_field'])) ? $params['search_field'] : 'created_at';
            if (!$searchModel->hasAttribute($search_field)) {
                throw new ServerErrorHttpException(Yii::t("app", "search field $search_field not found"));
            }
            $dataProvider->query->andFilterWhere(['between', $search_field, $start_date, $end_date]);
        }

        if (isset($params['order_type'])) {
            if (isset($params['order_by'])) {
                $dataProvider->query->orderBy($params['order_by'] . ' ' . $params['order_type']);
            } else {
                $dataProvider->query->orderBy('id ' . $params['order_type']);
            }
        }

        if (isset($params['conditions'])) {
            $arrayConditions = [];
            parse_str($params['conditions'], $arrayConditions);
            if (isset($params['condition_type'])) {
                $type = 'andwhere';
                $params['condition_type'] = strtolower($params['condition_type']);
                if ($params['condition_type'] == 'or')
                    $type = 'orwhere';


                foreach ($arrayConditions as $key => $value) {
                    if (array_shift($arrayConditions) == $key) {
                        $dataProvider->query->andwhere(['like', $key, $value]);
                    } else {
                        if ($value == 'null') {
                            $dataProvider->query->$type([$key => NULL]);
                        } else {
                            $dataProvider->query->$type(['like', $key, $value]);
                        }
                    }
                }
            } else {
                foreach ($arrayConditions as $key => $value) {
                    if ($value == 'null') {
                        $dataProvider->query->andWhere([$key => NULL]);
                    } else {
                        $dataProvider->query->andWhere(['like', $key, $value]);
                    }
                }
            }
        }

        if (isset($params['per_page'])) {
            if ($params['per_page'] > 500) {
                throw new ForbiddenHttpException('Limit 500 records per page');
            } else {
                $dataProvider->pagination->pageSize = $params['per_page'];
            }
        }

        if (isset($params['number'])) {
            $dataProvider->query->andWhere(['id' => $params['number']]);
        }

        return $dataProvider;
    }

    public function actionCreate()
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass([
            'scenario' => $this->createScenario,
        ]);

        $postContent = file_get_contents("php://input");
        $data = \app\models\Util::validatePostData($postContent);

        $model->load($data, '');
        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    public function actionUpdate($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);

        $model->scenario = $this->updateScenario;
        $request = Yii::$app->request;
        $postContent = file_get_contents("php://input");
        $data = \app\models\Util::validatePostData($postContent);

        if (empty($data)) { //Al ser PUT y enviar por FORM DATA
            parse_str(file_get_contents("php://input"), $data);
        }

        $model->load($data, '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $model;
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (
            !Yii::$app->user->identity->is_admin
        ) {
            throw new ForbiddenHttpException(Yii::t("app", "Access denied"));
        }

        if (is_object($model)) {
            $model->delete();
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t("app", "Object not found: $id"));
        }
    }

    public function findModel($id)
    {
        $modelClass = $this->modelClass;
        $model = $modelClass::find()->andWhere(['id' => $id])->one();

        if (isset($model)) {

            return $model;
        }
        throw new NotFoundHttpException(Yii::t("app", "Object not found: $id"));
    }
}
