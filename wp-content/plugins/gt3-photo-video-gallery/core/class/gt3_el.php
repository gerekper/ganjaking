<?php

	class gt3_el extends gt3classStd {

		protected static $fields_list = array();
		protected static $fields_list_array = array();


		protected $attr = array();
		protected $data = array();
		protected $class = array();
		protected $style = array();
		public $content;

		protected $is_sort = true;

		public $tag;

		public function __construct( $tag = 'div' ) {
			$this->tag     = $tag;
			$this->content = new ArrayObject();
		}

		public function sort( $sort = true ) {
			if ( $sort == true ) {
				$this->is_sort = true;
			} else {
				$this->unsort();
			}
		}

		public function unsort() {
			$this->is_sort = false;
		}

		public function setTag( $tag ) {
			$this->tag = htmlspecialchars( $tag );

			return $this;
		}

		public function addAttr( $name, $value ) {
			$name                = htmlspecialchars( $name );
			$value               = htmlspecialchars( $value );
			$this->attr[ $name ] = $value;

			return $this;
		}

		public function addAttrs( $attrs = array() ) {
			if ( is_array( $attrs ) && count( $attrs ) ) {
				foreach ( $attrs as $key => $value ) {
					if ( is_array( $value ) && count( $value ) > 1 ) {
						$this->attr[ htmlspecialchars( $value[0] ) ] = htmlspecialchars( $value[1] );
					} else {
						$value              = htmlspecialchars( $value );
						$this->attr[ $key ] = $value;
					}
				}
			}

			return $this;
		}

		public function getAttr( $name ) {
			if ( key_exists( $name, $this->attr ) ) {
				return $this->attr[ $name ];
			} else {
				return false;
			}
		}

		public function removeAttr( $name ) {
			if ( key_exists( $name, $this->attr ) ) {
				unset( $this->attr[ $name ] );
			}

			return $this;
		}

		public function clearAttrs() {
			$this->attr = array();

			return $this;
		}

		public function Style( $name ) {
			$name        = htmlspecialchars( $name );
			$this->style = array( $name => $name );
		}

		public function addStyle( $name, $value ) {
			$name                 = htmlspecialchars( $name );
			$value                = htmlspecialchars( $value );
			$this->style[ $name ] = $value;

			return $this;
		}

		public function addStyles( $styles = array() ) {
			if ( is_array( $styles ) && count( $styles ) ) {
				foreach ( $styles as $key => $value ) {
					if ( is_array( $value ) && count( $value ) > 1 ) {
						$this->style[ htmlspecialchars( $value[0] ) ] = htmlspecialchars( $value[1] );
					} else {
						$value               = htmlspecialchars( $value );
						$this->style[ $key ] = $value;
					}
				}
			}

			return $this;
		}

		public function getStyle( $name ) {
			if ( key_exists( $name, $this->style ) ) {
				return $this->style[ $name ];
			} else {
				return false;
			}
		}

		public function removeStyle( $name ) {
			if ( key_exists( $name, $this->style ) ) {
				unset( $this->style[ $name ] );
			}

			return $this;
		}

		public function clearStyles() {
			$this->style = array();

			return $this;
		}

		public function Data( $name ) {
			$name       = htmlspecialchars( $name );
			$this->data = array( $name => $name );
		}

		public function addData( $name, $value ) {
			$name                = htmlspecialchars( $name );
			$value               = htmlspecialchars( $value );
			$this->data[ $name ] = $value;

			return $this;
		}

		public function addDatas( $data = array() ) {
			if ( is_array( $data ) && count( $data ) ) {
				foreach ( $data as $key => $value ) {
					if ( is_array( $value ) && count( $value ) > 1 ) {
						$this->data[ htmlspecialchars( $value[0] ) ] = htmlspecialchars( $value[1] );
					} else {
						$value              = htmlspecialchars( $value );
						$this->data[ $key ] = $value;
					}
				}
			}

			return $this;
		}

		public function getData( $name ) {
			if ( key_exists( $name, $this->data ) ) {
				return $this->data[ $name ];
			} else {
				return false;
			}
		}

		public function removeData( $name ) {
			if ( key_exists( $name, $this->data ) ) {
				unset( $this->data[ $name ] );
			}

			return $this;
		}

		public function clearDatas() {
			$this->data = array();

			return $this;
		}

		public function setClass( $name ) {
			$name        = htmlspecialchars( $name );
			$this->class = array( $name => $name );
		}

		public function addClass( $class ) {
			$class                 = htmlspecialchars( $class );
			$this->class[ $class ] = $class;

			return $this;
		}

		public function addClasses( $classes = array() ) {
			if ( is_array( $classes ) && count( $classes ) ) {
				foreach ( $classes as $key => $value ) {
					$value               = htmlspecialchars( $value );
					$this->class[ $key ] = $value;
				}
			}

			return $this;
		}

		public function removeClass( $name ) {
			if ( key_exists( $name, $this->class ) ) {
				unset( $this->class[ $name ] );
			}

			return $this;
		}

		public function clearClasses() {
			$this->attr = array();

			return $this;
		}

		public function Content( $name ) {
			$name = htmlspecialchars( $name );
			$this->addContent( $name );
		}


		public function addContent( $pos, $content = null ) {
			if ( $content == null ) {
				$this->content[] = $pos;
			} else {
				$this->content[ $pos ] = $content;
			}

			return $this;
		}

		public function addContents( $content = array() ) {
			if ( is_array( $content ) && count( $content ) ) {
				foreach ( $content as $key => $value ) {
					$this->addContent( $key, $value );
				}
			}

			return $this;
		}

		public function removeContent( $pos ) {
			if ( $this->content->offsetExists( $pos ) ) {

				unset( $this->content[ $pos ] );
			}
		}

		public static function Create( $tag = 'div' ) {
			return new gt3_el( $tag );
		}

		public function render() {
			echo $this->__toString();
		}

		private function is_pair( $tag ) {
			return ( in_array( $tag, array(
				'a',
				'abbr',
				'address',
				'acronym',
				'applet',
				'article',
				'aside',
				'audio',
				'b',
				'bdi',
				'bdo',
				'big',
				'blink',
				'blockquote',
				'body',
				'button',
				'canvas',
				'caption',
				'center',
				'cite',
				'code',
				'colgroup',
				'comment',
				'datalist',
				'dd',
				'del',
				'details',
				'dfn',
				'dialog',
				'dir',
				'div',
				'dl',
				'dt',
				'em',
				'fieldset',
				'figcaption',
				'figure',
				'font',
				'footer',
				'form',
				'frame',
				'frameset',
				'head',
				'header',
				'hgroup',
				'html',
				'i',
				'iframe',
				'ins',
				'kbd',
				'label',
				'legend',
				'li',
				'listing',
				'map',
				'mark',
				'marquee',
				'menu',
				'meter',
				'multicol',
				'nav',
				'nobr',
				'noembed',
				'noframes',
				'noscript',
				'object',
				'ol',
				'optgroup',
				'option',
				'output',
				'p',
				'plaintext',
				'pre',
				'progress',
				'q',
				'rp',
				'rt',
				'ruby',
				's',
				'samp',
				'script',
				'section',
				'select',
				'small',
				'spacer',
				'span',
				'strike',
				'strong',
				'style',
				'sub',
				'summary',
				'sup',
				'table',
				'tbody',
				'td',
				'textarea',
				'tfoot',
				'th',
				'thead',
				'time',
				'title',
				'tr',
				'tt',
				'u',
				'ul',
				'var',
				'video',
				'xmp',
				'h1',
				'h2',
				'h3',
				'h4',
				'h5',
				'h6'
			) ) );
		}

		public function __toString() {
			// TODO: Implement __toString() method.
			$return = '<' . $this->tag;
			if ( is_array( $this->attr ) && count( $this->attr ) ) {
				foreach ( $this->attr as $key => $value ) {
					if ( $value == null ) {
						$return .= ' ' . $key;
					} else {
						$return .= ' ' . $key . '="' . $value . '"';
					}
				}
			}

			if ( is_array( $this->class ) && count( $this->class ) ) {
				$this->class = implode( ' ', $this->class );
				$return      .= ' class="' . $this->class . '"';
			}

			if ( is_array( $this->data ) && count( $this->data ) ) {
				foreach ( $this->data as $key => $value ) {
					$return .= ' data-' . $key . '="' . $value . '"';
				}
			}

			if ( is_array( $this->style ) && count( $this->style ) ) {
				$return .= ' style="';
				foreach ( $this->style as $key => $value ) {
					$return .= ' ' . $key . ': ' . $value . ';';
				}
				$return .= '"';
			}

			if ( $this->is_pair( $this->tag ) ) {
				$return .= '>';
				if ( $this->content instanceof ArrayObject && count( $this->content ) ) {
					if ( $this->is_sort ) {
						$this->content->ksort();
					}
					foreach ( $this->content as $key => $value ) {
						$return .= $value;
					}
				}
				$return .= '</' . $this->tag . '>';
			} else {
				$return .= ' />';
			}

			return $return . PHP_EOL;
		}
	}

