<?php

namespace App\Controllers;

use App\Exceptions\LogicException;

/**
 * @Anonymous
 */
class AuthController extends Controller
{
    public function loginAction()
    {
        if ($this->request->get('User') !== 'root' || $this->request->get('Password') !== '123!=321') {
            throw new LogicException('账号密码错误', 400);
        }
        $this->session->set('Id', 1);
        $this->response->setJsonContent([
            'message' => '登录成功'
        ]);
    }
}