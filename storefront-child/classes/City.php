<?php

/**
 * Class City
 */
class City
{
    /**
     * City constructor.
     *
     * Callbacks initialization
     */
    public function __construct()
    {
        add_action('init', [ $this, 'city_custom_post_type' ]);
        add_action('init', [ $this, 'city_type_taxonomy' ]);

        add_action( 'after_setup_theme', [ $this, 'city_thumb_size' ] );

        add_action('city_data', [ $this, 'city_data_output' ]);
        add_action('city_list', [ $this, 'city_list_output' ]);
        add_action('city_item', [ $this, 'city_list_item_output' ]);
    }

    /**
     * Custom post type registration
     */
    public function city_custom_post_type()
    {
        register_post_type('city', [
            'labels' => [
                'name'               => __('Cities', TEXT_DOMAIN),
                'singular_name'      => __('City', TEXT_DOMAIN),
                'add_new'            => __('Add city', TEXT_DOMAIN),
                'add_new_item'       => __('Add new city', TEXT_DOMAIN),
                'edit_item'          => __('Edit city', TEXT_DOMAIN),
                'new_item'           => __('New city', TEXT_DOMAIN),
                'view_item'          => __('View city', TEXT_DOMAIN),
                'search_items'       => __('Search city', TEXT_DOMAIN),
                'not_found'          => __('Cities not found', TEXT_DOMAIN),
                'not_found_in_trash' => __('Not found in trash', TEXT_DOMAIN),
                'parent_item_colon'  => __('', TEXT_DOMAIN),
                'menu_name'          => __('Cities', TEXT_DOMAIN),
            ],
            'public'              => true,
            'publicly_queryable'  => true,
            'exclude_from_search' => true,
            'show_ui'             => true,
            'show_in_nav_menus'   => true,
            'show_in_menu'        => true,
            'show_in_admin_bar'   => true,
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'supports'            => ['title', 'thumbnail'],
            'has_archive'         => true,
            'rewrite'             => true,
            'query_var'           => true
        ]);

        foreach (CITY_DATA_FIELD_SLUGS as $slug) {
            register_meta('post', $slug, [
                'object_subtype'    => 'city',
                'type'              => 'string',
                'description'       => '',
                'default'           => '',
                'single'            => false,
                'sanitize_callback' => null,
                'auth_callback'     => null,
                'show_in_rest'      => false,
            ]);
        }
    }

    /**
     * Custom taxonomy registration
     */
    public function city_type_taxonomy()
    {
        $labels = [
            'name'                       => _x( 'Countries', 'taxonomy general name', TEXT_DOMAIN ),
            'singular_name'              => _x( 'Country', 'taxonomy singular name', TEXT_DOMAIN ),
            'search_items'               => __( 'Search Country', TEXT_DOMAIN ),
            'popular_items'              => __( 'Popular Country', TEXT_DOMAIN ),
            'all_items'                  => __( 'All Countries', TEXT_DOMAIN ),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __( 'Edit Country', TEXT_DOMAIN ),
            'update_item'                => __( 'Update Country', TEXT_DOMAIN ),
            'add_new_item'               => __( 'Add New Country', TEXT_DOMAIN ),
            'new_item_name'              => __( 'New Country Title', TEXT_DOMAIN ),
            'separate_items_with_commas' => __( 'Separate countries with commas', TEXT_DOMAIN ),
            'add_or_remove_items'        => __( 'Add or remove countries', TEXT_DOMAIN ),
            'choose_from_most_used'      => __( 'Choose from the most used countries', TEXT_DOMAIN ),
            'not_found'                  => __( 'No countries found.', TEXT_DOMAIN ),
            'menu_name'                  => __( 'Countries', TEXT_DOMAIN ),
        ];

        $args = [
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        ];

        register_taxonomy('country', 'city', $args);
    }

    /**
     * Getting all cities as WP_Post[]
     *
     * @return WP_Post[]
     */
    public static function get_all()
    {
        return get_posts([
            'post_type' => 'city',
            'posts_per_page' => -1
        ]);
    }

    /**
     * Getting all cities with countries and temperatures
     *
     * @param int $cityId
     * @return array
     */
    public static function get_all_with_countries($cityId = 0)
    {
        global $wpdb;

        $cityIdValue = $cityId ? $cityId : '%';

        $cities_data = $wpdb->get_results("
            SELECT
                post_title,
                post_id,
                country_name,
                MAX(IF(latitude = '', NULL, latitude)) as latitude,
                MAX(IF(longitude = '', NULL, longitude)) as longitude
            FROM (
                SELECT 
                    p.post_title,
                    p.ID as post_id,
                    t.name as country_name,
                    t.term_id as country_id,
                    (CASE WHEN pm.meta_key = 'city_latitude' THEN pm.meta_value ELSE '' END) as latitude,
                    (CASE WHEN pm.meta_key = 'city_latitude' THEN '' ELSE pm.meta_value END) as longitude
                FROM
                    {$wpdb->prefix}posts p
                LEFT JOIN
                    {$wpdb->prefix}postmeta pm ON 
                        pm.post_id = p.ID AND 
                        (pm.meta_key = 'city_latitude' OR pm.meta_key = 'city_longitude')
                LEFT JOIN
                    {$wpdb->prefix}term_relationships tr ON tr.object_id = p.ID
                LEFT JOIN
                    {$wpdb->prefix}term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                LEFT JOIN
                    {$wpdb->prefix}terms t ON tt.term_id = t.term_id
                WHERE
                    p.post_type = 'city' AND
                    p.post_status = 'publish' AND
                    p.ID like '{$cityIdValue}'
                ORDER BY p.ID DESC
            ) r
            GROUP BY r.post_id
        ", ARRAY_A);

        $cities_with_temperatures = Weather::get_current_temperature($cities_data);

        $cities_data = [];

        foreach ($cities_with_temperatures as $city) {
            if (empty($cities_data[$city['country_name']])) {
                $cities_data[$city['country_name']] = [];
            }

            $cities_data[$city['country_name']][] = $city;
        }

        return $cities_data;
    }

    /**
     *  Custom thumb size registration
     *
     */
    public function city_thumb_size()
    {
        add_theme_support('post-thumbnails');

        if (function_exists('add_image_size')) {
            add_image_size('city-list-item', 500, 300, true);
        }
    }
}