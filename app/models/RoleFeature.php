<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class RoleFeature extends Model
{
    use CloudTrait;

    public $RoleId;

    public $FeatureId;

    public function getSource()
    {
        return 'RoleFeature';
    }
}