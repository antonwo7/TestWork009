<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', TEXT_DOMAIN); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_name('city_widget_city'); ?>"><?php _e('City:', TEXT_DOMAIN); ?></label>
    <select class="widefat" id="<?php echo $this->get_field_name('city_widget_city'); ?>" name="<?php echo $this->get_field_name('city_widget_city'); ?>">
        <?php foreach ($cities as $city) : ?>
            <option <?php echo $widget_city_value === $city->ID ? 'selected' : ''; ?> value="<?php echo $city->ID; ?>"><?php echo get_the_title($city->ID); ?></option>
        <?php endforeach; ?>
    </select>
</p>