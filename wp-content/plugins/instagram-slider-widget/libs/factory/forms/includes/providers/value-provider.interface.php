<?php
	/**
	 * The file contains an interface for all value provides.
	 *
	 * A value provider is a provide to get and save values to some stores (database, metadata and so on).
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package factory-forms
	 * @since 1.0.0
	 */
	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}
	if( !class_exists('Wbcr_IFactoryForms436_ValueProvider') ) {
		/**
		 * The interface for all value provides.
		 *
		 * @since 1.0.0
		 */
		interface Wbcr_IFactoryForms436_ValueProvider {

			/**
			 * Inits a form a provider to get data from a storage.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function init();

			/**
			 * Commits all changes.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function saveChanges();

			/**
			 * Gets a value by its name.
			 *
			 * @since 1.0.0
			 * @param string $name A value name to get.
			 * @param mixed|null $default A default to return if a given name doesn't exist.
			 * @param string $name A value name to get.
			 * @return mixed
			 */
			public function getValue($name, $default = null, $multiple = false);

			/**
			 * Sets a value by its name.
			 *
			 * @since 1.0.0
			 * @param string $name A value name to set.
			 * @param mixed $value A value to set.
			 * @return void
			 */
			public function setValue($name, $value);
		}
	}