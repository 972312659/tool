<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class Action extends Model
{
    use CloudTrait;

    const Anonymous = 0;

    const Authorize = 1;

    public $Id;

    public $Controller;

    public $Action;

    public $FeatureId;

    public $Type;

    public $Discard;

    public function getSource()
    {
        return 'Action';
    }
}