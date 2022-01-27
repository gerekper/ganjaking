<?php

namespace Ademti\DismissibleWpNotices;

class DismissibleWpNotice {
	/**
	 * @var string
	 */
	private $slug = '';

	/**
	 * @var bool
	 */
	private $per_user = false;

	/**
	 * @var int
	 */
	private $snooze_duration;

	/**
	 * @var bool
	 */
	private $per_site = false;

	/**
	 * @param string $slug
	 * @param bool|null $per_user
	 * @param int|float|null $snooze_duration
	 * @param bool|null $per_site
	 */
	public function __construct(
		string $slug,
		?bool $per_user = false,
		?int $snooze_duration = WEEK_IN_SECONDS,
		?bool $per_site = false
	) {
		$this->slug            = $slug;
		$this->per_user        = $per_user;
		$this->snooze_duration = $snooze_duration;
		$this->per_site        = $per_site;
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function __get( string $key ) {
		if ( isset( $this->$key ) ) {
			return $this->$key;
		}
		throw new \Exception( 'Invalid property access.' );
	}
}
