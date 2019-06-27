<?php

namespace App\Controllers;

use App\Exceptions\LogicException;
use App\Exceptions\ParamException;
use App\Models\Action;
use App\Models\DefaultFeature;
use App\Models\Feature;
use App\Models\RoleFeature;

class FeatureController extends Controller
{
    public function allAction()
    {
        $this->response->setJsonContent(Feature::tree());
    }

    public function createAction()
    {
        $e = new ParamException(400);
        $amount = Action::count([
            'conditions' => 'FeatureId=?0',
            'bind'       => [$this->request->get('PId')],
        ]);
        if ($amount > 0) {
            throw new LogicException('该节点上存在路由，无法添加子节点', 400);
        }
        $feature = new Feature();
        $feature->PId = $this->request->get('PId');
        $feature->Name = $this->request->get('Name');
        $feature->Url = $this->request->get('Url', null, null);
        $feature->Sign = $this->request->get('Sign', null, null);
        $feature->Icon = $this->request->get('Icon', null, null);
        if (!$feature->save()) {
            $e->loadFromModel($feature);
            throw $e;
        }
        $this->response->setJsonContent([
            'message' => '添加成功',
        ]);
    }

    public function updateAction()
    {
        $e = new ParamException(400);
        /** @var Feature $feature */
        $feature = Feature::findFirst($this->request->get('Id'));
        if (!$feature) {
            throw new LogicException('数据不存在', 400);
        }
        $feature->Name = $this->request->get('Name');
        $feature->Url = $this->request->get('Url', null, null);
        $feature->Sign = $this->request->get('Sign', null, null);
        $feature->Icon = $this->request->get('Icon', null, null);
        if (!$feature->save()) {
            $e->loadFromModel($feature);
            throw $e;
        }
        $this->response->setJsonContent([
            'message' => '修改成功',
        ]);
    }

    /**
     * 转移关系
     * 将叶子节点所有机构与角色之间的关系转移到目标节点
     * 若目标节点不是叶子节点，则向下遍历出其所有叶子节点用以建立关系
     */
    public function transferAction()
    {
        $e = new ParamException(400);
        /** @var Feature $feature */
        $feature = Feature::findFirst($this->request->get('Id'));
        if (!$feature) {
            throw new LogicException('数据不存在', 400);
        }
        //要移入的pid
        $newPId = $this->request->get('NewPId');
        if (Feature::findFirst(['conditions' => 'PId=?0', 'bind' => [$feature->Id]])) {
            throw new LogicException('该节点下存在子节点，不能移动', 400);
        }
        $feature->PId = $newPId;
        if (!$feature->save()) {
            $e->loadFromModel($feature);
            throw $e;
        }
        $this->response->setJsonContent([
            'message' => '修改成功',
        ]);
    }

    /**
     * 删除节点
     */
    public function deleteAction()
    {
        /** @var Feature $feature */
        $feature = Feature::findFirst($this->request->get('Id'));
        if (!$feature) {
            throw new LogicException('数据不存在', 400);
        }
        if (Feature::findFirst(['conditions' => 'PId=?0', 'bind' => [$feature->Id]])) {
            throw new LogicException('该节点下存在子节点，不能删除', 400);
        }
        // $roleCount = $this->request->get('RoleCount');
        // if (isset($roleCount) && is_numeric($roleCount) && $roleCount > 0) {
        //     throw new LogicException('该节点存在用户角色，不能删除', 400);
        // }

        //删除对应角色的权限
        $this->dbcloud->begin();
        // 删除指定Type的默认feature
        $sql = 'DELETE FROM RoleFeature WHERE `FeatureId`=?';
        $this->dbcloud->execute($sql, [$feature->Id]);

        $feature->delete();
        $this->dbcloud->commit();
        $this->response->setJsonContent([
            'message' => '删除成功',
        ]);
    }

    /**
     * 获取指定机构类型的功能
     * 参数:
     *      Type int 医院=1/网点=2/供应商=3
     */
    public function defaultAction()
    {
        $result = DefaultFeature::find([
            'conditions' => 'Type=?0',
            'bind'       => [$this->request->get('Type')],
        ]);
        $this->response->setJsonContent($result ?: []);
    }

    /**
     * 修改排序
     */
    public function sortAction()
    {
        $e = new ParamException(400);
        $feature = Feature::findFirst($this->request->get('Id'));
        if (!$feature) {
            throw new LogicException('数据不存在', 400);
        }
        if (!is_numeric($this->request->get('Sort'))) {
            throw new LogicException('必须是整数', 400);
        }
        $feature->Sort = $this->request->get('Sort');
        if (!$feature->save()) {
            $e->loadFromModel($feature);
            throw $e;
        }
        $this->response->setJsonContent([
            'message' => '修改成功',
        ]);
    }

    /**
     * 获取指定机构类型的功能
     * 参数:
     *      Type int 医院=1/网点=2/供应商=3
     *      Features []int 叶子节点的id
     */
    public function setDefaultAction()
    {
        try {
            $exception = new ParamException(400);
            $features = $this->request->get('Features');
            $this->dbcloud->begin();
            // 删除指定Type的默认feature
            $sql = 'DELETE FROM DefaultFeature WHERE `Type`=?';
            $this->dbcloud->execute($sql, [$this->request->get('Type')]);
            if (!\is_array($features)) {
                throw new LogicException('参数错误', 400);
            }
            foreach ($features as $featureId) {
                $defaultFeature = new DefaultFeature();
                $defaultFeature->Type = $this->request->get('Type');
                $defaultFeature->FeatureId = $featureId;
                if (!$defaultFeature->save()) {
                    $exception->loadFromModel($defaultFeature);
                    throw $exception;
                }
            }
            //删除角色对应的没有的权限
            $featureIds = '(' . implode(',', $features) . ')';
            $this->dbcloud->execute("DELETE FROM RoleFeature WHERE FeatureId NOT IN {$featureIds}");
            $this->dbcloud->commit();
            $this->dispatcher->forward(['action' => 'default']);
        } catch (\Exception $e) {
            $this->dbcloud->rollback();
            throw new LogicException($e->getMessage(), 400);
        }
    }
}