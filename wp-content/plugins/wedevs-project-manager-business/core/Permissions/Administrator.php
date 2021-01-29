<?php

namespace WeDevs\PM_Pro\Core\Permissions;

use WeDevs\PM_Pro\Core\Permissions\Abstract_Permission;
use WP_REST_Request;

class Administrator extends Abstract_Permission {
    public function check() {
        return true;
    }
}
