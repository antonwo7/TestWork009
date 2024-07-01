<?php


class Weather
{
    /**
     * @param array $data
     * @return array
     */
    public static function get_current_temperature(array $data)
    {
        try {
            if (empty($data)) return [];

            $post_data = ['locations' => []];

            foreach ($data as $i => $item) {
                if (!isset($item['latitude']) || !isset($item['longitude'])) {
                    continue;
                }

                $post_data['locations'][] = [
                    'custom_id' => isset($item['post_id']) ? $item['post_id'] : $i,
                    'q' => "{$item['latitude']},{$item['longitude']}"
                ];
            }

            $apiUrl = WEATHER_API_URL . '?key=' . WEATHER_API_KEY . '&q=bulk';

            $result = wp_remote_post($apiUrl, [
                'headers'     => ['Content-Type' => 'application/json; charset=utf-8'],
                'body'        => json_encode($post_data),
                'method'      => 'POST',
                'data_format' => 'body',
            ]);

            if (is_wp_error($result)) {
                return [];
            }

            if (empty($result['body'])) {
                return [];
            }

            $result = json_decode($result['body'], true);
            if (empty($result) || empty($result['bulk'])) {
                return [];
            }

            $temp = array_reduce($result['bulk'], function($carry, $item) {
                if (isset($item['query']['custom_id']) && isset($item['query']['current']['temp_c'])) {
                    $carry[$item['query']['custom_id']] = $item['query']['current']['temp_c'];
                }

                return $carry;
            }, []);

            foreach ($data as $i => $item) {
                $data[$i]['temperature'] = $temp[$item['post_id']];
            }

            return $data;

        } catch (Exception $e) {
            return [];
        }
    }
}