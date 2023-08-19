<?php

namespace ACA\ACF\Editing;

use ACP;

interface EditingFactoryAware extends ACP\Editing\Editable {

	public function set_editing_model_factory( EditingModelFactory $factory );

}