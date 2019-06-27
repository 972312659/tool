<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 2019/6/4
 * Time: 9:29 AM
 */

namespace App\Controllers;


use App\Exceptions\LogicException;
use App\Exceptions\ParamException;
use App\Models\Module;
use App\Models\ModuleFeature;

class ModuleController extends Controller
{
    public function moduleListAction()
    {
        $this->response->setJsonContent(Module::find());
    }

    public function moduleFeatureListAction()
    {
        $moduleFeature = ModuleFeature::find([
            'conditions' => 'ModuleCode=?0',
            'bind'       => [$this->request->get('ModuleCode')],
        ])->toArray();
        $this->response->setJsonContent($moduleFeature);
    }


    public function updateAction()
    {
        try {
            $exception = new ParamException(400);
            $features = $this->request->getPut('Features');
            $moduleCode = $this->request->getPut('ModuleCode');
            $this->dbcloud->begin();

            $moduleFeatures = ModuleFeature::find([
                'conditions' => 'ModuleCode=?0',
                'bind'       => [$moduleCode],
            ]);

            $changed = false;
            $add = [];
            $moduleFeatureArr = $moduleFeatures->toArray();
            if (empty($moduleFeatureArr)) {
                if (!empty($features)) {
                    $add = $features;
                }
            } else {
                $moduleFeatureIds = array_column($moduleFeatureArr, 'FeatureId');
                if (empty($features)) {
                    $moduleFeatures->delete();
                    $changed = true;
                } else {
                    foreach ($features as $f) {
                        if (!in_array($f, $moduleFeatureIds)) {
                            $changed = true;
                            $add[] = $f;
                        }
                    }

                    foreach ($moduleFeatures as $item) {
                        /** @var ModuleFeature $item */
                        if (!in_array($item->FeatureId, $features)) {
                            $changed = true;
                            $item->delete();
                        }
                    }
                }
            }

            if (!empty($add)) {
                foreach ($add as $featureId) {
                    $moduleFeature = new ModuleFeature();
                    $moduleFeature->SysCode = 'yun-web';
                    $moduleFeature->ParentCode = '';
                    $moduleFeature->ModuleCode = $moduleCode;
                    $moduleFeature->FeatureId = $featureId;
                    $moduleFeature->AddUser = 'init';
                    $moduleFeature->AddTime = date('Y-m-d H:i:s');
                    $moduleFeature->ModifyUser = 'init';
                    $moduleFeature->ModifyTime = date('Y-m-d H:i:s');
                    $moduleFeature->IsDelete = 0;
                    if (!$moduleFeature->save()) {
                        $exception->loadFromModel($moduleFeature);
                        throw $exception;
                    }
                }
            }

            if ($changed) {
                $this->redis->delete('__DEFAULT_FEATURE__');
                $keys = $this->redis->getKeys('Cache:OrganizationFeature:*');
                if (!empty($keys)) {
                    foreach ($keys as $key) {
                        $this->redis->delete($key);
                    }
                }
            }

            $this->dbcloud->commit();
            $this->response->setJsonContent(['message' => '操作成功']);
        } catch (\Exception $e) {
            $this->dbcloud->rollback();
            throw new LogicException($e->getMessage(), 400);
        }
    }
}