<?php

namespace GlueAgency\CDN\models;

use craft\base\Model;

class Settings extends Model
{

    public $baseUrl = '';

    public $signKey = '';

    public $getExternalImageDimensions = false;

    public $defaultParams = [];
}
