# Laravel Translate
### Laravel command to translate text using Google Cloud Translate V2

## Features

- Translate text from Laravel's current language to any language supported by Google API
- Keep existing text intact, so you don't lose your manual translations

## Requirements

- There must be at least one language file in the `resources/lang` folder (e.g.: `en.json`)

## How to Use it

1. Install [Google Cloud Translate](https://packagist.org/packages/google/cloud-translate) via composer using `composer require google/cloud-translate`
2. Copy the file `Translate.php` into your `app/Console/Commands` folder
3. Update the `Translate.php` file at line 33 and fill the `$googleCloudKey` with your Google API Key
4. Open the terminal and run the command `php artisan translate:lang de`, replacing `de` for the language you want to translate to, and follow the instructions

## How to get the Google API Key

1. Go to [Google Cloud Platform](https://console.cloud.google.com/)
2. Create a new project
3. Go to the project and click on `APIs & Services` on the left menu
4. Click on `Credentials` on the left menu
5. Click on `Create Credentials` and select `API Key`