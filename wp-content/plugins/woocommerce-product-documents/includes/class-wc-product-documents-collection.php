<?php
/**
 * WooCommerce Product Documents
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Documents to newer
 * versions in the future. If you wish to customize WooCommerce Product Documents for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-documents/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Product Documents Collection class, contains a set of product document sections
 *
 * @since 1.0
 */
class WC_Product_Documents_Collection {


	/** @var int Optional product identifier */
	private $product_id;

	/** @var \WC_Product_Documents_Section[] Array of WC_Product_Documents_Section objects */
	private $sections;


	/**
	 * Initialize an empty set of product documents
	 *
	 * @since 1.0
	 * @param int $product_id optional product identifier
	 */
	public function __construct( $product_id = null ) {

		$this->sections = array();

		// load any persisted documents from the provided product
		if ( $product_id ) {
			$this->load_from_product( $product_id );
		}
	}


	/**
	 * Adds the given section to the collection of documents
	 *
	 * @since 1.0
	 * @param \WC_Product_Documents_Section $section a section of documents
	 * @param int $index optional index at which to set the section, defaults to the final position
	 */
	public function add_section( $section, $index = null ) {

		if ( ! is_null( $index ) ) {
			$this->sections[ $index ] = $section;
		} else {
			$this->sections[] = $section;
		}
	}


	/**
	 * Returns the document sections
	 *
	 * @since 1.0
	 * @param boolean $include_empty true to include empty sections, false otherwise.  Defaults to false
	 * @return \WC_Product_Documents_Section[] Array of WC_Product_Documents_Section section objects
	 */
	public function get_sections( $include_empty = false ) {

		$sections = array();

		if ( $include_empty ) {

			$sections = $this->sections;

		} else {

			// filter out empty sections (those without at least one document with a configured file location)
			foreach ( $this->sections as $section ) {

				// add non-empty sections
				if ( $section->has_documents( false ) ) {
					$sections[] = $section;
				}
			}
		}

		return apply_filters( 'wc_product_documents_get_sections', $sections, $this, $include_empty );
	}


	/**
	 * Returns true if there is one or more sections in this collection
	 *
	 * @since 1.0
	 * @param boolean $include_empty true to include empty sections, false otherwise.  Defaults to false
	 * @return boolean true if there are any sections with documents, false otherwise
	 */
	public function has_sections( $include_empty = false ) {

		$sections = $this->get_sections( $include_empty );

		// no sections with configured documents
		return ! empty( $sections );
	}


	/**
	 * Returns the index of the section marked as default, if any
	 *
	 * @since 1.0
	 * @param boolean $include_empty use true to include empty sections, false otherwise.  Defaults to false
	 * @return mixed index of default section, if any, otherwise false
	 */
	public function get_default_section_index( $include_empty = false ) {

		foreach ( $this->get_sections( $include_empty ) as $index => $section ) {

			if ( $section->is_default() ) {
				return $index;
			}
		}

		// no default section found
		return false;
	}


	/**
	 * Loads any configured sections and documents from the identified product
	 *
	 * @since 1.0
	 * @param int $product_id product identifier
	 */
	private function load_from_product( $product_id ) {

		$this->sections    = array();
		$this->product_id  = $product_id;
		$product           = $product_id > 0 ? wc_get_product( $product_id ) : null;
		$product_documents = $product ? $product->get_meta( '_wc_product_documents' ) : null;

		if ( is_array( $product_documents ) ) {

			foreach ( $product_documents as $section ) {

				$documents = array();

				// create the set of documents
				foreach ( $section['documents'] as $document ) {
					$documents[] = new WC_Product_Documents_Document( $document['label'], $document['file_location'] );
				}

				// add the section
				$this->add_section( new WC_Product_Documents_Section( $section['name'], $section['default'], $documents ) );
			}
		}
	}


	/**
	 * Persists the product documents collection to the identified product.
	 *
	 * @since 1.0
	 *
	 * @param int $product_id The product identifier
	 * @param \WC_Product $product The product object
	 */
	public function save_to_product( $product_id, $product = null ) {

		$product           = ! $product instanceof \WC_Product ? wc_get_product( $product_id ) : $product;
		$product_documents = array();

		foreach ( $this->get_sections( true ) as $section ) {

			$documents = array();

			foreach ( $section->get_documents( true ) as $document ) {
				$documents[] = array( 'label' => $document->get_label( true ), 'file_location' => $document->get_file_location() );
			}

			$product_documents[] = array( 'name' => $section->get_name(), 'default' => $section->is_default(), 'documents' => $documents );
		}

		$product->update_meta_data( '_wc_product_documents', $product_documents );
		$product->save_meta_data();
	}


	/**
	 * Returns the product id that these documents are a part of, if any
	 *
	 * @since 1.1.1
	 * @return int product id or null
	 */
	public function get_product_id() {
		return $this->product_id;
	}


} // end \WC_Product_Documents_Collection class


/**
 * Product Documents Section class.
 * This represents a set of product documents.
 *
 * @since 1.0
 */
class WC_Product_Documents_Section {

	/** @var string Section name */
	private $name;

	/** @var bool true if this section is the default one */
	private $is_default;

	/** @var \WC_Product_Documents_Document[] array Array of WC_Product_Documents_Document objects */
	private $documents;


	/**
	 * Initializes a set of product documents.
	 *
	 * @since 1.0
	 *
	 * @param string $name optional section name, defaults to empty string
	 * @param boolean $is_default true if this section is the default one, optional defaults to false
	 * @param \WC_Product_Documents_Document[] $documents optional array of WC_Product_Documents_Document document objects, defaults to empty array
	 */
	public function __construct( $name = '', $is_default = false, $documents = array() ) {

		$this->name       = $name;
		$this->is_default = $is_default;
		$this->documents  = $documents;
	}


	/**
	 * Adds the given document to the collection of documents for this section.
	 *
	 * @since 1.0
	 *
	 * @param \WC_Product_Documents_Document $document a document
	 * @param int $index optional index at which to set the document, defaults to the final position
	 */
	public function add_document( $document, $index = null ) {

		if ( ! is_null( $index ) ) {
			$this->documents[ $index ] = $document;
		} else {
			$this->documents[] = $document;
		}
	}


	/**
	 * Returns the section name.
	 *
	 * @since 1.0
	 *
	 * @return string section name, or null
	 */
	public function get_name() {

		return is_string( $this->name ) ? stripslashes( $this->name ) : $this->name;
	}


	/**
	 * Returns true if this is the default section.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function is_default() {

		return $this->is_default;
	}


	/**
	 * Returns the documents for this section.
	 *
	 * @since 1.0
	 *
	 * @param bool $include_empty true to include empty documents, false otherwise. Defaults to false
	 * @return \WC_Product_Documents_Document[] array of WC_Product_Documents_Document document objects
	 */
	public function get_documents( $include_empty = false ) {

		if ( $include_empty ) {

			// return all documents, including those that may be empty
			return $this->documents;

		} else {

			// filter out empty documents (those without a configured file location)
			$documents = array();

			foreach ( $this->documents as $document ) {

				// add the document if not empty
				if ( ! $document->is_empty() ) {
					$documents[] = $document;
				}
			}

			return $documents;
		}
	}


	/**
	 * Returns true if this section has any documents.
	 *
	 * @since 1.0
	 *
	 * @param bool $include_empty true to include empty documents, false otherwise. Defaults to false
	 * @return bool
	 */
	public function has_documents( $include_empty = false ) {

		$documents = $this->get_documents( $include_empty );

		return ! empty( $documents );
	}


} // end \WC_Product_Documents_Section class


/**
 * Product Document class, represents a document.
 *
 * @since 1.0
 */
class WC_Product_Documents_Document {


	/** @var string document label */
	private $label;

	/** @var string the URL or path to the document file */
	private $file_location;


	/**
	 * Initializes a product document.
	 *
	 * @since 1.0
	 *
	 * @param string $label optional document label, defaults to empty string
	 * @param string $file_location optional URL or path to the document file, defaults to empty string
	 */
	public function __construct( $label = '', $file_location = '' ) {

		$this->label         = $label;
		$this->file_location = $file_location;
	}


	/**
	 * Returns the displayable file label, or the filename otherwise.
	 *
	 * @since 1.0
	 *
	 * @param bool $raw true will return the raw label value, false returns the display value.  defaults to false
	 * @return string the displayable file label, or null
	 */
	public function get_label( $raw = false ) {

		// use display label if set
		if ( $this->label || $raw ) {
			$label = $this->label;
		// otherwise default to file name
		} elseif ( $this->file_location ) {
			$label = basename( $this->file_location );
		} else {
			$label = null;
		}

		return is_string( $label ) ? stripslashes( $label ) : null;
	}


	/**
	 * Returns the URL or path to the document file.
	 *
	 * @since 1.0
	 *
	 * @return string the URL or path to the document file, or null
	 */
	public function get_file_location() {

		return $this->file_location;
	}


	/**
	 * Returns true if this document is not fully configured (has no file location).
	 *
	 * @since 1.0
	 *
	 * @return bool true if this document is empty
	 */
	public function is_empty() {

		$file_location = $this->get_file_location();

		return empty( $file_location );
	}


} // end \WC_Product_Documents_Document class
