<?php

namespace GlueAgency\CDN\models;

use craft\base\Model;

class Settings extends Model
{

    public $baseUrl = 'https://cdn.glue.be';

    public $apiKey = '';

    public $getExternalImageDimensions = false;

    public $defaultParams = [];
}
