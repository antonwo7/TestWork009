<?php


class Assets
{
    /**
     * Assets constructor.
     *
     * Custom scripts and styles loading
     */
    function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 100);
    }

    /**
     * Callback for scripts and styles loading
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script(
            'theme-js',
            SCRIPTS_URI . '/main.js',
            false
        );

        wp_localize_script('theme-js', 'THEME_VARS', [
            'get_cities_api_endpoint_url' => THEME_API_URI
        ]);

        wp_enqueue_style(
            'theme-css',
            STYLES_URI . '/main.css'
        );
    }
}