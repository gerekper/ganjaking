<div class="profileDashboard dashboardRight" id = "dashboard-my-posts">
    <?php
        global $_POST;
        global $userpro;
        if ( is_user_logged_in() ):
            $temp = new UPDBAjax();
            echo $temp->updb_pagination(false,1);
        endif;
 ?>
</div>