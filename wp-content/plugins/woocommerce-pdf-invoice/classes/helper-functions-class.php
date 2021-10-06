<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_pdf_invoice_helper_functions {

    public function __construct() {
		
		global $wpdb,$woocommerce;


	}

	/**
	 * [get_template_folder description]
	 * @param  string $location [description]
	 * @return [type]           [description]
	 */
	public static function get_template_folder( $location = 'plugin' ) {

		if( $location == 'theme' ) {
			return get_stylesheet_directory() . '/pdf_templates/';
		} 
		
		return PDFPLUGINPATH . 'templates/';
		
	}

	/**
	 * [get_default_template_files description]
	 * @param  boolean $remove_paths     [description]
	 * @param  boolean $remove_extension [description]
	 * @return [type]                    [description]
	 */
	public static function get_template_files( $location = 'plugin', $remove_paths = true, $remove_extension = true ) {

		$folder 	= WC_pdf_invoice_helper_functions::get_template_folder( $location );
		$templates 	= glob( $folder . '*.php' );
		$return 	= array();
		$paths 		= array();
		$extension 	= array();

		// Remove the paths
		if( $remove_paths && 0 !== count( $templates ) ) {

			foreach( $templates as $template ) {
	            $filename = str_replace( $folder, '', $template );
	            $paths[] = $filename;
	        }

	        unset( $templates );
	        $templates = array();
	        $templates = $paths;
	    }

	    // Remove the extension
		if( $remove_extension && 0 !== count( $templates ) ) {

			foreach( $templates as $template ) {
	            $filename = str_replace( '.php', '', $template );
	            $extension[] = $filename;
	        }

	        unset( $templates );
	        $templates = array();
	        $templates = $extension;
	    }

	    if( 0 !== count( $templates ) ) {

			foreach( $templates as $template ) {
	            $return[$template] = $template;
	        }
	    }
	    
	    return $return;

	}

	/**
	 * [is_template_there description]
	 * @param  [type]  $filename [description]
	 * @param  string  $location [description]
	 * @return boolean           [description]
	 */
	public static function is_template_there( $filename, $location = 'plugin' ) {

		// Add .php if necessary
		if( strpos($filename, '.php' ) === false) {
			$filename =  $filename . '.php';
		}

		$folder = WC_pdf_invoice_helper_functions::get_template_folder( $location );
		$path 	= $folder . $filename;

		if( file_exists( $path ) ) {
			return true;
		}

		return false;

	}

	/**
	 * [get_duplicate_form description]
	 * @param  string $template [description]
	 * @param  [type] $email    [description]
	 * @return [type]           [description]
	 */
	public static function get_duplicate_form( $template, $email ) {

			return '<form method="post" action="" name="pdf_invoice_duplicate_template_filename_form">
				<table class="dompgf-debugging-table">
				  <tr>
				      <th colspan="2">' . __("Enter your template filename, do not add a file extension, use only letters, numbers, _ and -" , "woocommerce-pdf-invoice" ) . '</th>
				  </tr>
				  <tr>
				      <td><input type="text" name="pdf_invoice_duplicate_template_filename_field" placeholder="Your filename"/></td>
				      <td>
				          <input type="hidden" name="pdf_invoice_duplicate_template_filename_check" value="' . MD5( $email ) . '" />
				          <input type="hidden" name="pdf_invoice_duplicate_template_original_filename" value="' . $template . '" />
				          <input type="submit" class="dompgf-debugging-submit" value="' . __("Duplicate the PDF template file" , "woocommerce-pdf-invoice" ) . '" />
				      </td>
				  </tr>
				</table>
			</form>';
	}

}

$GLOBALS['WC_pdf_invoice_helper_functions'] = new WC_pdf_invoice_helper_functions();