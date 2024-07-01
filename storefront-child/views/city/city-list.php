<table>
    <tbody>
    <?php foreach ($data as $country_title => $cities) : ?>
        <tr>
            <th><?php echo $country_title; ?></th>
            <?php foreach ($cities as $city) : ?>
                <td>
                    <?php echo $city['post_title'] . ' (' . $city['temperature'] . '&deg;C)'; ?>
                </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>