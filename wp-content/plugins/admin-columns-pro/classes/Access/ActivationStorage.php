<?php

namespace ACP\Access;

use AC\Storage\KeyValueFactory;
use AC\Storage\KeyValuePair;
use ACP\Entity\Activation;
use ACP\Type\Activation\ExpiryDate;
use ACP\Type\Activation\Key;
use ACP\Type\Activation\Products;
use ACP\Type\Activation\RenewalMethod;
use ACP\Type\Activation\Status;
use ACP\Type\ActivationToken;
use DateTime;
use Exception;

final class ActivationStorage {

	const ACTIVATION_DETAILS = 'acp_subscription_details';
	const ACTIVATION_TOKEN = 'acp_subscription_details_key';

	const PARAM_STATUS = 'status';
	const PARAM_RENEWAL_METHOD = 'renewal_method';
	const PARAM_EXPIRY_DATE = 'expiry_date';
	const PARAM_PRODUCTS = 'products';

	/**
	 * @var KeyValuePair
	 */
	private $activation;

	/**
	 * @var KeyValuePair
	 */
	private $token;

	public function __construct( KeyValueFactory $option_factory ) {
		$this->activation = $option_factory->create( self::ACTIVATION_DETAILS );
		$this->token = $option_factory->create( self::ACTIVATION_TOKEN );
	}

	/**
	 * @param ActivationToken $activation_token
	 *
	 * @return Activation|null
	 */
	public function find( ActivationToken $activation_token ) {
		if ( $this->token->get() !== $activation_token->get_token() ) {
			return null;
		}

		$data = $this->activation->get();

		if ( empty( $data ) ) {
			return null;
		}

		// Check required params
		$params = [
			self::PARAM_STATUS,
			self::PARAM_RENEWAL_METHOD,
			self::PARAM_EXPIRY_DATE,
			self::PARAM_PRODUCTS,
		];

		foreach ( $params as $param ) {
			if ( ! array_key_exists( $param, $data ) ) {
				return null;
			}
		}

		if ( ! Status::is_valid( $data[ self::PARAM_STATUS ] ) ) {
			return null;
		}

		if ( ! RenewalMethod::is_valid( $data[ self::PARAM_RENEWAL_METHOD ] ) ) {
			return null;
		}

		if ( null === $data[ self::PARAM_EXPIRY_DATE ] ) {
			$expire_date = null;
		} else {
			try {
				$expire_date = new DateTime();
				$expire_date->setTimestamp( $data[ self::PARAM_EXPIRY_DATE ] );
			} catch ( Exception $e ) {
				return null;
			}
		}

		return new Activation(
			new Status( $data[ self::PARAM_STATUS ] ),
			new RenewalMethod( $data[ self::PARAM_RENEWAL_METHOD ] ),
			new ExpiryDate( $expire_date ),
			new Products( $data[ self::PARAM_PRODUCTS ] ?: [] )
		);
	}

	public function save( Key $key, Activation $activation ) {
		$data = [
			self::PARAM_STATUS         => $activation->get_status()->get_value(),
			self::PARAM_RENEWAL_METHOD => $activation->get_renewal_method()->get_value(),
			self::PARAM_EXPIRY_DATE    => $activation->get_expiry_date()->exists() ? $activation->get_expiry_date()->get_value()->getTimestamp() : null,
			self::PARAM_PRODUCTS       => $activation->get_products()->get_value(),
		];

		$this->activation->save( $data );
		$this->token->save( $key->get_token() );
	}

	public function delete() {
		$this->activation->delete();
		$this->token->delete();
	}

}