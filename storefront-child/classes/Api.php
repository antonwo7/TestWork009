<?php


class Api
{
    /**
     * Api constructor.
     *
     * REST API initializing and callbacks binding
     */
    function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(
                'city',
                '/get_all',
                ['methods'  => 'GET', 'callback' => [$this, 'get_cities']]
            );
        });
    }

    /**
     * Output all cities with country terms and temperatures
     *
     * @param WP_REST_Request $request
     * @return void
     */
    public function get_cities(WP_REST_Request $request)
    {
        $params = $request->get_params();

        $city_id = !empty($params['city_id']) ? intval($params['city_id']) : 0;

        $data = City::get_all_with_countries($city_id);

        include(VIEWS_PATH . '/city/city-list.php');
        exit;
    }
}