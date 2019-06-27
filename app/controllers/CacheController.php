<?php

namespace App\Controllers;

class CacheController extends Controller
{
    const CLOUD_REDIS_INDEX = 1;

    /**
     * 获取apcu缓存信息
     */
    public function listApcuAction()
    {
        $this->response->setJsonContent(apcu_cache_info(false));
    }

    /**
     * 删除指定key的apcu缓存，并返回apcu缓存信息
     */
    public function delApcuCacheAction()
    {
        apcu_delete($this->request->get('Key'));
        $this->response->setJsonContent(apcu_cache_info(false));
    }

    /**
     * 清空apcu缓存，并apcu缓存信息
     */
    public function clearApcuCacheAction()
    {
        apcu_clear_cache();
        $this->response->setJsonContent(apcu_cache_info(false));
    }

    /**
     * 清除存在redis上的model meta data
     * 用于更改了数据库字段时使用
     */
    public function clearRedisModelCacheAction()
    {
        $result = [];
        $this->redis->select(self::CLOUD_REDIS_INDEX);
        do {
            $keys = $this->redis->scan($ret, '_PH*');
            if (\is_array($keys)) {
                $result = array_merge($result, $keys);
            }
        } while ($ret > 0);
        $count = 0;
        if ($result) {
            $count = $this->redis->del($result);
        }
        $this->response->setJsonContent([
            'message' => "共计清除掉 $count 条缓存",
        ]);
    }
}