<?php

namespace WeDevs\PM_Pro\Core\Database;

use WeDevs\PM_Pro\Core\Database\Migration;

abstract class Abstract_Migration implements Migration {
    public function run() {
        $this->schema();
    }

    abstract public function schema();
}