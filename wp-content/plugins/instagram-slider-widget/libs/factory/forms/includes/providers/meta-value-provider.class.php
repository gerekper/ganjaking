<?php
	/**
	 * The file contains the class of Factory Meta Value Provider.
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
	
	if( !class_exists('Wbcr_FactoryForms436_OptionsValueProvider') ) {
		
		/**
		 * Factory Meta Value Provider
		 *
		 * This provide works with meta values like a lazy key-value storage and
		 * provides methods to commit changes on demand. It increases perfomance on form saving.
		 *
		 * @since 1.0.0
		 */
		class Wbcr_FactoryForms436_MetaValueProvider implements Wbcr_IFactoryForms436_ValueProvider {


			public $scope;

			protected $post_id;

			/**
			 * Values to save $metaName => $metaValue
			 * @var array
			 */
			private $values = array();
			
			/**
			 * Chanched meta keys (indexed array)
			 * @var array
			 */
			private $keys = array();
			
			private $meta = array();

			
			/**
			 * Creates a new instance of a meta value provider.
			 *
			 * @param array $options
			 */
			public function __construct($options = array())
			{
				global $post;
				
				$this->scope = (isset($options['scope']))
					? $options['scope']
					: null;
				
				$this->scope = preg_replace('/\_meta\_box$/', '', $this->formatCamelCase($this->scope));
				
				/*$this->post_id = (isset($options['post_id']))
					? $options['post_id']
					: $post->ID;
				
				// the second parameter for compatibility with wordpress 3.0
				$temp = get_post_meta($this->post_id, '', true);
				
				foreach($temp as $key => &$content) {
					if( strpos($key, $this->scope) === 0 ) {
						$this->meta[$key] = $content;
					}
				}*/

				$this->init();
			}
			
			/**
			 * Initizalize an instance of the provider.
			 * This method should be invoked before the provider usage.
			 *
			 * @param bool $post_id
			 */
			public function init($post_id = false)
			{
				global $post;
				
				$this->post_id = $post_id
					? $post_id
					: $post->ID;
				
				// the second parameter for compatibility with wordpress 3.0
				$temp = get_post_meta($this->post_id, '', true);

				foreach($temp as $key => &$content) {
					if( strpos($key, $this->scope) === 0 ) {
						$this->meta[$key] = $content;
					}
				}
			}
			
			/**
			 * Saves changes into a database.
			 * The method is optimized for bulk updates.
			 */
			public function saveChanges()
			{
				
				$this->deleteValues();
				$this->insertValues();
				/**
				 * foreach ($this->values as $key => $value) {
				 * update_post_meta($this->postId, $key, $value);
				 * }
				 */
			}
			
			/**
			 * Removes all actual values from a database.
			 */
			private function deleteValues()
			{
				if( count($this->keys) == 0 ) {
					return;
				}
				
				global $wpdb;

				$values = array();
				$keys[] = $this->post_id;

				for($i = 0; $i < count($this->keys); $i++) {
					$values[] = '%s';
					$keys[] = $this->keys[$i];
				}

				$clause = implode(',', $values);
				$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE post_id='%d' AND meta_key IN ($clause)", $keys));
			}
			
			/**
			 * /**
			 * Inserts new values by using bulk insert directly into a database.
			 *
			 * @return bool|false|int
			 */
			private function insertValues()
			{
				global $wpdb;
				
				$sql = "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) VALUES ";
				$rows = array();
				
				foreach($this->values as $meta_key => $meta_value) {
					if( is_array($meta_value) ) {
						foreach($meta_value as $value) {
							$rows[] = $wpdb->prepare('(%d,%s,%s)', $this->post_id, $meta_key, $value);
						}
					} else {
						$rows[] = $wpdb->prepare('(%d,%s,%s)', $this->post_id, $meta_key, $meta_value);
					}
				}
				
				if( empty($rows) ) {
					return false;
				}
				
				$sql = $sql . implode(',', $rows);
				
				return $wpdb->query($sql);
			}
			
			/**
			 * @param string $name
			 * @param null $default
			 * @param bool $multiple
			 * @return array|int|null
			 */
			public function getValue($name, $default = null, $multiple = false)
			{
				if( is_array($name) ) {
					
					$values = array();
					$index = 0;
					
					foreach($name as $item) {
						$item_default = ($default && is_array($default) && isset($default[$index]))
							? $default[$index]
							: null;
						
						$values[] = $this->getValueBySingleName($item, $item_default, $multiple);
						$index++;
					}
					
					return $values;
				}
				
				$value = $this->getValueBySingleName($name, $default, $multiple);
				
				return $value;
			}
			
			/**
			 * @param $single_name
			 * @param null $default
			 * @param bool $multiple
			 * @return int|null
			 */
			protected function getValueBySingleName($single_name, $default = null, $multiple = false)
			{
				
				$value = isset($this->meta[$this->scope . '_' . $single_name])
					? ($multiple)
						? $this->meta[$this->scope . '_' . $single_name]
						: $this->meta[$this->scope . '_' . $single_name][0]
					: $default;
				
				if( $value === 'true' ) {
					$value = 1;
				}
				if( $value === 'false' ) {
					$value = 0;
				}
				
				return $value;
			}
			
			/**
			 * @param string $name
			 * @param mixed $value
			 */
			public function setValue($name, $value)
			{
				
				if( is_array($name) ) {
					$index = 0;
					
					foreach($name as $item) {
						$itemValue = ($value && is_array($value) && isset($value[$index]))
							? $value[$index]
							: null;
						
						$this->setValueBySingleName($item, $itemValue);
						$index++;
					}
					
					return;
				}
				
				$this->setValueBySingleName($name, $value);
				
				return;
			}
			
			/**
			 * @param string $single_name
			 * @param mixed $singe_value
			 */
			protected function setValueBySingleName($single_name, $singe_value)
			{
				$name = $this->scope . '_' . $single_name;
				
				if( is_array($singe_value) ) {
					
					foreach($singe_value as $index => $value) {
						
						$singe_value[$index] = empty($singe_value[$index])
							? $singe_value[$index]
							: stripslashes($singe_value[$index]);
					}
					
					$value = $singe_value;
				} else {
					$value = empty($singe_value)
						? $singe_value
						: stripslashes($singe_value);
				}
				
				$this->values[$name] = $value;
				$this->keys[] = $name;
			}
			
			/**
			 * @param string $string
			 * @return string
			 */
			private function formatCamelCase($string)
			{
				$output = "";
				foreach(str_split($string) as $char) {
					if( strtoupper($char) == $char && !in_array($char, array('_', '-')) ) {
						$output .= "_";
					}
					$output .= $char;
				}
				$output = strtolower($output);
				
				return $output;
			}
		}
	}