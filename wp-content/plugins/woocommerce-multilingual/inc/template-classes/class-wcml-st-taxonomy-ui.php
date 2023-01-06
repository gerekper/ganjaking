<?php

class WCML_St_Taxonomy_UI extends WCML_Templates_Factory {

	private $taxonomy_obj;

	public function __construct( $taxonomy_obj ) {
		parent::__construct();

		$this->taxonomy_obj = $taxonomy_obj;
	}

	public function get_model() {

		if ( $this->taxonomy_obj->publicly_queryable ) {
			$model = [
				'link_url'   => admin_url( 'admin.php?page=wpml-wcml&tab=slugs' ),
				/* translators: %s is a taxonomy name */
				'link_label' => sprintf( __( 'Set different slugs in different languages for %s on WooCommerce Multilingual & Multicurrency URLs translations page',
					'woocommerce-multilingual' ), $this->taxonomy_obj->labels->name ),
			];
		} else {
			$attrid = wc_attribute_taxonomy_id_by_name( $this->taxonomy_obj->name );
			$model  = [
				'link_url'   => admin_url( 'edit.php?post_type=product&page=product_attributes&edit=' . $attrid ),
				/* translators: %s is an attribute name */
				'link_label' => sprintf( __( 'To translate the attribute slug, please set the option "Enable archives?" for the attribute %s in WooCommerce',
					'woocommerce-multilingual' ), $this->taxonomy_obj->labels->name ),
			];

		}

		return $model;

	}

	public function render() {

		return $this->show();
	}

	protected function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/',
		];
	}

	public function get_template() {
		return 'st-taxonomy-ui.twig';
	}

}
