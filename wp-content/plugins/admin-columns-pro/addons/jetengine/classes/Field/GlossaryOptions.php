<?php

namespace ACA\JetEngine\Field;

interface GlossaryOptions {

	/**
	 * @return bool
	 */
	public function has_glossary_options();

	/**
	 * @return array
	 */
	public function get_glossary_options();

}