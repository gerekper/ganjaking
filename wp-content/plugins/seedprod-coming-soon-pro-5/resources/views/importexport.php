<?php
        $post_id = absint($_GET['id']);


        if (!empty($_POST['sp_post_json'])) {
            // update
            global $wpdb;
            $json = json_decode(stripslashes($_POST['sp_post_json']));
            if (json_last_error() !== JSON_ERROR_NONE) {
                wp_die('JSON is NOT valid');
            }
            $json = json_encode($json);
            $tablename = $wpdb->prefix . 'posts';
            $r = $wpdb->update(
                $tablename,
                array(
                'post_content_filtered' => $json,	// string
            ),
                array( 'ID' => $post_id ),
                array(
                '%s',	// value1
            ),
                array( '%d' )
            );
            if ($r === false) {
                echo 'Update error'. PHP_EOL;
            } else {
                echo 'Updated'. PHP_EOL;
            }
        }

        global $wpdb;
        $tablename = $wpdb->prefix . 'posts';
        $sql = "SELECT * FROM $tablename";
        $sql .= " WHERE ID = %s" ;
        $safe_sql = $wpdb->prepare($sql, $post_id);
        $result = $wpdb->get_row($safe_sql);


        $js = json_decode($result->post_content_filtered);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo 'JSON is valid'. PHP_EOL;
        } else {
            echo 'JSON is NOT valid'. PHP_EOL;
        }

    
?>
<form method="post">
<h1>Post JSON</h1>
<textarea name="sp_post_json" style="width:100%; height: 500px;"><?php echo $result->post_content_filtered; ?></textarea>
<input type="submit">
</form>
