<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class OrganizationFeature extends Model
{
    use CloudTrait;

    public $OrganizationId;

    public $FeatureId;

    public function getSource()
    {
        return 'OrganizationFeature';
    }
}