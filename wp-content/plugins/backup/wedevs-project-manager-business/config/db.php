<?php

return [
    'driver'    => 'mysql',
    'host'      => pm_pro_wp_config( 'DB_HOST' ),
    'database'  => pm_pro_wp_config( 'DB_NAME' ),
    'username'  => pm_pro_wp_config( 'DB_USER' ),
    'password'  => pm_pro_wp_config( 'DB_PASSWORD' )
];