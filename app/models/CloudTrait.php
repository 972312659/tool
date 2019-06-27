<?php
/**
 * Created by IntelliJ IDEA.
 * User: void
 * Date: 2018/10/9
 * Time: 16:17
 */

namespace App\Models;


trait CloudTrait
{
    public function initialize()
    {
        $this->setConnectionService('dbcloud');
    }
}