<?php $data = City::get_all_with_countries(); ?>

<section>
    <div>
        <div class="city_filter">
            <select name="city" id="city_select">
                <option value=""><?php echo __('All Cities', TEXT_DOMAIN); ?></option>
                <?php foreach(array_merge(...array_values($data)) as $city) : ?>
                    <option value="<?php echo $city['post_id']; ?>">
                        <?php echo $city['post_title']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php do_action('before_city_list_action'); ?>
        <div id="city_list">
            <?php include(VIEWS_PATH . '/city/city-list.php'); ?>
        </div>
        <?php do_action('after_city_list_action'); ?>
    </div>
</section>