# Glue CDN for Imager X

Glue Agency image CDN for Imager X

## Requirements

This plugin requires Craft CMS 4.3.5 or later, and PHP 8.0.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require glue-agency/craft-glue-cdn-imager-x

# tell Craft to install the plugin
php craft plugin/install glue-cdn-imager-x
```

## Configuration

Create the `glue-cdn-imager-x.php` file in `/config` and add the following.

```php
return [
    'baseUrl'       => rtrim(getenv('GLUE_CDN_DOMAIN'), '/'),
    'signKey'       => getenv('GLUE_CDN_SIGN_KEY'),
    'defaultParams' => [
        'dpr' => 2,
    ],
];
```

Update the Imager-X transformer to `glue-cdn`.
