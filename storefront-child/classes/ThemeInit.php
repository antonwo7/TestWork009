<?php

include_once __DIR__ . '/City.php';
include_once __DIR__ . '/Coordinates.php';
include_once __DIR__ . '/CityWidget.php';
include_once __DIR__ . '/Weather.php';
include_once __DIR__ . '/Api.php';
include_once __DIR__ . '/Assets.php';

class ThemeInit
{
    /**
     * ThemeInit constructor.
     *
     * All theme classes initializing
     */
    function __construct()
    {
        new City();
        new Coordinates();
        new CityWidget();
        new Api();
        new Assets();
    }

    /**
     * Singleton implementation
     *
     * @return ThemeInit
     */
    public static function createInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }
}