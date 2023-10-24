<?php

namespace GlueAgency\CDN\models;

use craft\base\Model;

class Settings extends Model
{

    public string $baseUrl = 'https://cdn.glue.be';

    public string $apiKey = '';

    public bool $getExternalImageDimensions = true;

    public array $defaultParams = [];
}
