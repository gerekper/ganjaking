<?php

namespace ACA\Pods\Column;

use ACA\Pods\Column;

class Post extends Column {

	protected function get_pod_name() {
		return $this->get_post_type();
	}

}