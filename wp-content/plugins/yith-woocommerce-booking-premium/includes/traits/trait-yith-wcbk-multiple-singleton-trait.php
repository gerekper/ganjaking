<?php
/**
 * Multiple Singleton class trait.
 * Allows creating one different singleton for each called_class,
 * without needs of re-declaring the $instance property of the class
 * (as for YITH_WCBK_Singleton_Trait).
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Traits
 */

/**
 * Multiple Singleton trait.
 */
trait YITH_WCBK_Multiple_Singleton_Trait {

	/**
	 * The instances of the classes.
	 *
	 * @var self
	 */
	private static $instances = array();

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function __construct() {
	}

	/**
	 * Get class instance.
	 *
	 * @return self
	 */
	final public static function get_instance() {
		self::$instances[ static::class ] = self::$instances[ static::class ] ?? new static();

		return self::$instances[ static::class ];
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {
	}

	/**
	 * Prevent un-serializing.
	 */
	public function __wakeup() {
		yith_wcbk_doing_it_wrong( get_called_class() . '::' . __FUNCTION__, 'Unserializing instances of this class is forbidden.', '3.0' );
	}
}
