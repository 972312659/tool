<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 2019/5/9
 * Time: 12:11 PM
 */

namespace App\Controllers;

use App\Exceptions\ParamException;
use Phalcon\Di\FactoryDefault;

class SystemController extends Controller
{


    /**
     * 监控--mysql
     * @Anonymous
     */
    public function seeOneAction()
    {
        $ex = new ParamException(510);
        try {
            $config = FactoryDefault::getDefault()->get("config")->get("cloud");

            //对mysqli类进行实例化
            $mysqli = new \mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
            if (mysqli_connect_errno()) {    //判断是否成功连接上MySQL数据库
                shell_exec("sudo  systemctl restart mysql.service");
                throw $ex;  //如果连接错误，则抛出异常
            } else {
                echo '数据库连接成功！';   //打印连接成功的提示
            }
        } catch (ParamException $ex) {
            throw $ex;
        }

    }


    /**
     * 监控--redis
     * @Anonymous
     */
    public function seeTwoAction()
    {
        $ex = new ParamException(510);
        try {
            $redis = new \Redis();
            $config = FactoryDefault::getDefault()->get("config")->get("redis");
            $re = $redis->connect($config['host'], $config['port']);
            if (!$re) {
                throw $ex;
            }
        } catch (ParamException $ex) {
            throw $ex;
        }

    }

    /**
     * 监控--sphinx
     * @Anonymous
     */
    public function seeThreeAction()
    {
        $ex = new ParamException(510);
        try {
            $config = FactoryDefault::getDefault()->get("config")->get("sphinx");
            $link = mysqli_connect($config['host'], '', '', '', $config['port']);
            if (!$link) {
                throw $ex;
            }
        } catch (ParamException $ex) {
            throw $ex;
        }
    }
}