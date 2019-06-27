<?php

namespace App\Controllers;

use App\Exceptions\LogicException;
use App\Exceptions\ParamException;
use App\Models\Action;
use App\Models\Feature;
use Phalcon\Db\RawValue;

class RouteController extends Controller
{
    /**
     * 获取所有路由
     */
    public function allAction()
    {
        $actions = Action::find()->toArray();
        $result = [];
        foreach ($actions as $action) {
            $key = $action['Controller'];
            unset($action['Controller']);
            if ($action['FeatureId']) {
                $features = Feature::traceParent($action['FeatureId']);
                $list = array_column($features, 'Name');
                $action['Path'] = implode(' / ', $list);
            }
            $result[$key][] = $action;
        }
        $this->response->setJsonContent($result);
    }

    /**
     * 路由绑定feature
     * 参数:
     *      ActionId int
     *      FeatureId int
     * @throws LogicException
     * @throws ParamException
     */
    public function bindAction()
    {
        $featureId = (int)$this->request->get('FeatureId');
        if (!$featureId) {
            throw new LogicException('参数错误', 400);
        }
        $action = Action::findFirst($this->request->get('ActionId'));
        if (!$action) {
            throw new LogicException('路由不存在', 400);
        }
        if ($action->Discard === 1) {
            throw new LogicException('接口已丢弃', 400);
        }
        if ($action->Type === Action::Anonymous) {
            throw new LogicException('代码级的公开路由无法指定绑定', 400);
        }
        $exception = new ParamException(400);
        $action->FeatureId = $this->request->get('FeatureId');
        if (!$action->save()) {
            $exception->loadFromModel($action);
            throw $exception;
        }
        $features = Feature::traceParent($featureId);
        $list = array_column($features, 'Name');
        $this->response->setJsonContent([
            'message' => '绑定成功',
            'Path'    => implode(' / ', $list),
        ]);
    }

    /**
     * 路由解绑feature
     * 参数:
     *      ActionId int
     * @throws LogicException
     * @throws ParamException
     */
    public function unbindAction()
    {
        $action = Action::findFirst($this->request->get('ActionId'));
        if (!$action) {
            throw new LogicException('路由不存在', 400);
        }
        if ($action->Discard === 1) {
            throw new LogicException('接口已丢弃', 400);
        }
        $exception = new ParamException(400);
        $action->FeatureId = new RawValue('NULL');
        if (!$action->save()) {
            $exception->loadFromModel($action);
            throw $exception;
        }
        $this->response->setJsonContent([
            'message' => '解绑成功'
        ]);
    }
}