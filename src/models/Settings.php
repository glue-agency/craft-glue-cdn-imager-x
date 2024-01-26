<?php

namespace GlueAgency\CDN\models;

use craft\base\Model;

class Settings extends Model
{

    public string $baseUrl = '';

    public string $signKey = '';

    public bool $getExternalImageDimensions = false;

    public array $defaultParams = [];
}
