<?php

namespace WeDevs\PM_Pro\Core\Database;

interface Migration {
    public function schema();
    public function run();
}