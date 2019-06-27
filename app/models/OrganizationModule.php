<?php

namespace App\Models;

use Phalcon\Mvc\Model;


class OrganizationModule extends Model
{
    use CloudTrait;

    public $Id;
    public $OrganizationId;
    public $SysCode;
    public $ParentCode;
    public $ModuleCode;
    public $ValidTimeBeg;
    public $ValidTimeEnd;
    public $IsDisable;
    public $AddUser;
    public $AddTime;
    public $ModifyUser;
    public $ModifyTime;
    public $IsDelete;

    public function getSource()
    {
        return 'OrganizationModule';
    }
}