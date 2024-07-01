<?php

define('ASSETS_URI', get_stylesheet_directory_uri() . '/assets');
define('STYLES_URI', ASSETS_URI . '/css');
define('SCRIPTS_URI', ASSETS_URI . '/js');

define('VIEWS_PATH', get_stylesheet_directory() . '/views');

define('THEME_API_URI', get_site_url() . '/wp-json/city/get_all');

define('TEXT_DOMAIN', 'storefront-child');
define('CITY_DATA_FIELD_SLUGS', [
    'city_latitude' => 'Latitude',
    'city_longitude' => 'Longitude'
]);
define('WIDGET_SLUG', 'city_widget');
define('WEATHER_API_URL', 'http://api.weatherapi.com/v1/current.json');
define('WEATHER_API_KEY', '2c809f9ec9d74254aeb184325242906');