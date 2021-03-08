<?php

namespace MailOptin\Core\Repositories;


class OptinConversionsRepository extends AbstractRepository
{

    /**
     * Add new conversion data to database.
     *
     *
     * @param array $data {
     *     Array of conversion data
     *
     * @type string $optin_id
     * @type string $optin_type
     * @type string $name
     * @type string $email
     * @type string $user_agent
     * @type string $conversion_page
     * @type string $referrer
     * }
     *
     * @return false|int
     */
    public static function add($data)
    {
        $response = parent::wpdb()->insert(
            parent::conversions_table(),
            array(
                'optin_id'        => $data['optin_campaign_id'],
                'optin_type'      => $data['optin_campaign_type'],
                'name'            => $data['name'],
                'email'           => $data['email'],
                'custom_fields'   => $data['custom_fields'],
                'user_agent'      => $data['user_agent'],
                'conversion_page' => $data['conversion_page'],
                'referrer'        => $data['referrer'],
                'date_added'      => current_time('mysql')
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );

        return ! $response ? $response : self::wpdb()->insert_id;
    }

    /**
     * Update existing conversion data to database.
     *
     * @param int $id
     * @param array $data {
     *     Array of conversion data
     *
     * @type string $optin_id
     * @type string $optin_type
     * @type string $name
     * @type string $email
     * @type string $user_agent
     * @type string $conversion_page
     * @type string $referrer
     * }
     *
     * @return false|int
     */
    public static function update($id, $data)
    {
        $update_data = array(
            'optin_id'        => $data['optin_campaign_id'],
            'optin_type'      => $data['optin_campaign_type'],
            'name'            => $data['name'],
            'email'           => $data['email'],
            'user_agent'      => $data['user_agent'],
            'conversion_page' => $data['conversion_page'],
            'referrer'        => $data['referrer'],
            'date_added'      => current_time('mysql')
        );

        $update_data = array_filter($update_data, function ($value) {
            return ! empty($value);
        });

        return parent::wpdb()->update(
            parent::conversions_table(),
            $update_data,
            array('id' => $id),
            '%s'
        );
    }

    /**
     * Get a conversion data
     *
     * @param int $conversion_id
     *
     * @return mixed
     */
    public static function get($conversion_id)
    {
        $table = parent::conversions_table();

        return self::wpdb()->get_row(
            self::wpdb()->prepare("SELECT * FROM $table WHERE id = %d", $conversion_id), 'ARRAY_A');
    }

    /**
     * Get conversions data by IDs
     *
     * @param array $conversion_ids
     *
     * @return string
     */
    public static function get_conversions_by_ids($conversion_ids)
    {
        $table          = parent::conversions_table();
        $conversion_ids = array_map('absint', $conversion_ids);

        $sql = "SELECT * FROM $table WHERE id IN(" . implode(', ', array_fill(0, count($conversion_ids), '%s')) . ")";

        return self::wpdb()->get_results(
            call_user_func_array([self::wpdb(), 'prepare'], array_merge([$sql], $conversion_ids)),
            'ARRAY_A'
        );
    }

    /**
     * Get conversions data by email.
     *
     * @param string $email
     *
     * @return string
     */
    public static function get_conversions_by_email($email)
    {
        $table = parent::conversions_table();

        return self::wpdb()->get_results(
            self::wpdb()->prepare("SELECT * FROM $table WHERE email = %s", $email),
            'ARRAY_A'
        );
    }

    /**
     * Retrieve conversion data from the database
     *
     * @param int $limit
     * @param int $offset
     *
     * @return mixed
     */
    public static function get_conversions($limit = null, $offset = 1, $search = null)
    {
        if (is_null($search) && ! empty($_POST['s'])) {
            $search = $_POST['s'];
        }

        $table = parent::conversions_table();

        $sql = "SELECT * FROM {$table}";

        $replacements = [];

        if ( ! empty($search)) {
            $sql .= " WHERE name LIKE %s";
            $sql .= " OR email LIKE %s";

            $search = '%' . parent::wpdb()->esc_like(sanitize_text_field($_POST['s'])) . '%';

            $replacements[] = $search;
            $replacements[] = $search;
        }

        $sql .= " ORDER BY id DESC";
        if ( ! is_null($limit)) {
            $sql .= ' LIMIT %d';

            $replacements[] = $limit;
        }

        if ( ! is_null($limit)) {
            $offset = ($offset - 1) * $limit;
            if ($offset > 1) {
                $sql .= "  OFFSET %d";

                $replacements[] = $offset;
            }
        }

        if ( ! empty($replacements)) {
            array_unshift($replacements, $sql);
            $sql = call_user_func_array([parent::wpdb(), 'prepare'], $replacements);
        }

        $result = parent::wpdb()->get_results($sql, 'ARRAY_A');

        return $result;
    }

    /**
     * Delete a conversion record.
     *
     * @param int $id conversion ID
     *
     * @return false|int
     */
    public static function delete($id)
    {
        return parent::wpdb()->delete(
            parent::conversions_table(),
            array('id' => $id),
            array('%d')
        );
    }

    /**
     * Return count of optin conversion made today.
     *
     * @return null|string
     */
    public static function today_conversion_count()
    {
        $table = parent::conversions_table();

        return parent::wpdb()->get_var("SELECT COUNT(*) FROM $table WHERE DATE(date_added) = CURDATE()");
    }

    /**
     * Return count of optin conversion this month.
     *
     * @return int
     */
    public static function month_conversion_count()
    {
        $table = parent::conversions_table();

        return absint(parent::wpdb()->get_var("SELECT COUNT(*) FROM $table WHERE MONTH(date_added) = MONTH(CURRENT_DATE())"));
    }
}