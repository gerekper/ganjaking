<?php

namespace ACP\Editing\Service;

interface Editability {

	public function is_editable( int $id ): bool;

	public function get_not_editable_reason( int $id ): string;

}