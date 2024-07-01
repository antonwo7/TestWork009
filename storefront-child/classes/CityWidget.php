<?php

class CityWidget extends WP_Widget
{
    /**
     * CityWidget constructor.
     *
     * Widget initializing
     */
    function __construct()
    {
        parent::__construct(
            WIDGET_SLUG,
            __('City Widget', TEXT_DOMAIN),
            ['description' => __('City widget', TEXT_DOMAIN)]
        );

        add_action('widgets_init', [$this, 'load_widget']);
    }

    /**
     * Widget content output
     *
     * @param $args
     * @param $instance
     */
    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];

        if (!empty($title)) {
            echo "{$args['before_title']}{$title}{$args['after_title']}";
        }

        if (!empty($instance['city_widget_city']) && $city_post = get_post($instance['city_widget_city'])) {
            $city_title = get_the_title($city_post);
            $latitude = get_post_meta($city_post->ID, 'city_latitude', true);
            $longitude = get_post_meta($city_post->ID, 'city_longitude', true);

            if ($latitude && $longitude) {
                $temperatures = Weather::get_current_temperature([[
                    'post_id' => $instance['city_widget_city'],
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ]]);

                if (!empty($temperatures)) {
                    $city_data = array_values($temperatures)[0];
                    if (isset($city_data['temperature']) && $city_data['temperature'] !== null) {
                        $temperature = $city_data['temperature'];
                        include(VIEWS_PATH . '/widgets/city_widget/front.php');
                    }
                }
            }
        }

        echo $args['after_widget'];
    }

    /**
     * Widget form output while editing
     *
     * @param $instance
     */
    public function form($instance)
    {
        $title = isset($instance['title']) ? $instance['title'] : __('New title', TEXT_DOMAIN);
        $widget_city_value = isset($instance['city_widget_city']) ? $instance['city_widget_city'] : '';
        $cities = City::get_all();

        include(VIEWS_PATH . '/widgets/city_widget/form.php');
    }

    /**
     * Widget options updating
     *
     * @param $new_instance
     * @param $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['city_widget_city'] = (!empty($new_instance['city_widget_city'])) ? strip_tags($new_instance['city_widget_city']) : '';
        return $instance;
    }

    /**
     *  Widget registration
     */
    public function load_widget()
    {
        register_widget(__CLASS__);
    }
}