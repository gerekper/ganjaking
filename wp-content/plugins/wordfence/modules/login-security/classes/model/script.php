<?php

namespace WordfenceLS;

class Model_Script extends Model_Asset {

	public function enqueue() {
		if ($this->registered) {
			wp_enqueue_script($this->handle);
		}
		else {
			wp_enqueue_script($this->handle, $this->source, $this->dependencies, $this->version);
		}
	}

	public function isEnqueued() {
		return wp_script_is($this->handle);
	}

	public function renderInline() {
		if (empty($this->source))
			return;
?>
		<script type="text/javascript" src="<?php echo esc_attr($this->getSourceUrl()) ?>"></script>
<?php
	}

	public function register() {
		wp_register_script($this->handle, $this->source, $this->dependencies, $this->version);
		return parent::register();
	}

}