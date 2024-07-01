<?php


class Coordinates
{
    /**
     * Coordinates constructor.
     *
     * Actions callbacks binding
     */
    public function __construct()
    {
        add_action('add_meta_boxes_city', [$this, 'register_meta_box']);
        add_action('save_post', [$this, 'save_coordinates_meta_box_data']);
    }

    /**
     *  Coordinates Meta box registration
     */
    public function register_meta_box()
    {
        add_meta_box(
            'coordinates',
            __('Coordinates', TEXT_DOMAIN),
            [$this, 'coordinates_output']
        );
    }

    /**
     * Coordinates inputs output in admin panel
     *
     * @param object $post
     */
    public function coordinates_output(object $post)
    {
        wp_nonce_field('coordinates_nonce', 'coordinates_nonce');

        foreach (CITY_DATA_FIELD_SLUGS as $slug => $label) :
            $value = get_post_meta($post->ID, $slug, true); ?>

            <label for="<?php echo $slug; ?>">
                <?php echo __($label, TEXT_DOMAIN); ?>
                <input type="text" id="<?php echo $slug; ?>" name="<?php echo $slug; ?>" value="<?php echo esc_attr($value); ?>">
            </label>
        <?php endforeach;
    }

    /**
     * Saving Coordinates
     *
     * @param int $post_id
     */
    public function save_coordinates_meta_box_data(int $post_id)
    {
        if (!isset($_POST['coordinates_nonce'])) return;

        if (!wp_verify_nonce($_POST['coordinates_nonce'], 'coordinates_nonce')) return;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        if (isset($_POST['post_type']) && $_POST['post_type'] === 'city') {
            if (!current_user_can('edit_page', $post_id)) return;
        } else {
            if (!current_user_can('edit_post', $post_id)) return;
        }

        foreach (CITY_DATA_FIELD_SLUGS as $slug => $label) {
            if (!isset($_POST[$slug])) continue;

            $value = sanitize_text_field($_POST[$slug]);
            update_post_meta($post_id, $slug, $value);
        }
    }
}