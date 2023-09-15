<?php

namespace GroovyMenu;

use \GroovyMenu\WalkerNavMenu as WalkerNavMenu;
use \GroovyMenuStyle as GroovyMenuStyle;
use \GroovyMenuGFonts as GroovyMenuGFonts;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class FrontendWalker
 */
class FrontendSimpleWalker extends WalkerNavMenu {

	protected $currentLvl   = 0;
	protected $megaMenuPost = null;
	protected $currentItem;

	/**
	 * @param string $output
	 * @param int    $depth
	 * @param array  $args
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$this->currentLvl ++;
		parent::start_lvl( $output, $depth = 0, $args = array() );
	}

	/**
	 * @param string $output
	 * @param int    $depth
	 * @param array  $args
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$this->currentLvl --;
		parent::end_lvl( $output, $depth = 0, $args = array() );
	}

	/**
	 * Begin of element
	 *
	 * @param string   $output
	 * @param \WP_Post $item
	 * @param int      $depth
	 * @param array    $args
	 * @param int      $id
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $groovyMenuSettings;

		$item_output   = '';
		$hiding_symbol = array( '-', '–', '&#8211;' );

		$this->currentItem = $item;
		$indent            = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$link_before       = empty( $args->link_before ) ? '' : $args->link_before;
		$link_after        = empty( $args->link_after ) ? '' : $args->link_after;
		$item_before       = empty( $args->before ) ? '' : $args->before;
		$item_after        = empty( $args->after ) ? '' : $args->after;

		$show_in_mobile = ( isset( $args->gm_navigation_mobile ) && $args->gm_navigation_mobile );

		$this->megaMenuPost = $this->megaMenuPost( $item );

		$postContent = $this->getMenuBlockPostContent( $this->megaMenuPost );
		if ( function_exists( 'groovy_menu_add_custom_styles' ) ) {
			groovy_menu_add_custom_styles( $this->megaMenuPost );
		}
		if ( function_exists( 'groovy_menu_add_custom_styles_support' ) ) {
			groovy_menu_add_custom_styles_support( $this->megaMenuPost );
		}

		$gm_menu_block = false;
		if ( isset( $item->object ) && 'gm_menu_block' === $item->object ) {
			$gm_menu_block = true;
		}


		$headerStyle = intval( $groovyMenuSettings['header']['style'] );


		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$thumb   = null;

		$classes[] = 'gm-menu-item';
		$classes[] = 'gm-menu-item--lvl-' . $depth;


		if ( $this->frozenLink( $this->currentItem ) ) {
			$classes[] = 'gm-frozen-link';
		}

		if ( $this->preventAutoclose( $this->currentItem ) ) {
			$classes[] = 'gm-close-by-click-only';
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = trim( $class_names ) ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$mobile_id_prefix = $show_in_mobile ? 'mobile-' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $mobile_id_prefix . $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : ' id="menu-item-' . $mobile_id_prefix . esc_attr( $item->ID ) . '"';

		$output .= $indent . '<li' . $id . $class_names . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';
		$atts['class']  = 'gm-anchor';
		if ( $this->hasChildren( $classes ) ) {
			$atts['class'] .= ' gm-dropdown-toggle';
		}
		if ( $this->hasParents() ) {
			$atts['class'] .= ' gm-menu-item__link';
		}

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes       = '';
		$attributes_thumb = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				if ( 'href' === $attr ) {
					$value = esc_url( $value );
					if ( $gm_menu_block ) {
						$value = $this->menuBlockURL( $item, $value );
					}
				} else {
					$value = esc_attr( $value );
				}
				$attributes .= ' ' . $attr . '="' . $value . '"';
				if ( 'class' !== $attr ) {
					$attributes_thumb .= ' ' . $attr . '="' . $value . '"';
				} else {
					$attributes_thumb .= ' class="gm-menu-item-thumbnail"';
				}
			}
		}

		$item_output .= $item_before;
		if ( ! $this->doNotShowTitle( $item ) ) {

			if ( ! empty( $item->url ) ) {
				$item_output .= '<a' . $attributes . '>';
			} else {
				$item_output .= '<div class="' . $atts['class'] . ' gm-anchor--empty">';
			}

			$badge = array(
				'left'  => '',
				'right' => '',
			);

			// if exist custom HTML Icon - then show it first.
			if ( $this->getUseHtmlAsIcon( $item ) ) {
				$html_icon_content = $this->getHtmlIconContent( $item );
				if ( ! empty( $html_icon_content ) ) {
					$badge_content = '<div class="gm-menu-item__icon">' . $html_icon_content . '</div>';
					if ( 0 === $depth && in_array( $headerStyle, array( 4 ), true ) ) {
						$item_output .= $badge_content;
					} else {
						$badge['left'] .= $badge_content;
					}
				}

				// if exist classic Icon - then show it second.
			} elseif ( $this->getIcon( $item ) ) {
				$badge_content = '<span class="gm-menu-item__icon ' . $this->getIcon( $item ) . '"></span>';
				if ( 0 === $depth && in_array( $headerStyle, array( 4 ), true ) ) {
					$item_output .= $badge_content;
				} else {
					$badge['left'] .= $badge_content;
				}

				// if no one Icon set & we get icon sidebar or expanded sidebar - then show first letter of nav-0menu title.
			} elseif ( 0 === $depth && in_array( $headerStyle, array( 4, 5 ), true ) && ! $show_in_mobile ) {
				$badge_content = '<span class="gm-menu-item__icon">' . $this->getFirstLetterAsIcon( $item ) . '</span>';
				if ( 4 === $headerStyle ) {
					$item_output .= $badge_content;
				} else {
					$badge['left'] .= $badge_content;
				}
			}

			$badge_enable = $this->getBadgeEnable( $item );
			if ( ! empty( $badge_enable ) && $badge_enable ) {

				$attr                    = 'style';
				$badge_type              = $this->getBadgeType( $item );
				$badge_placement         = $this->getBadgePlacement( $item );
				$badge_position          = $this->getBadgeGeneralPosition( $item );
				$badge_y_position        = $this->getBadgeYPosition( $item ) ? : 0;
				$badge_x_position        = $this->getBadgeXPosition( $item ) ? : 0;
				$badge_container_radius  = $this->getBadgeContainerRadius( $item );
				$badge_container_padding = $this->getBadgeContainerPadding( $item );
				$badge_container_bg      = $this->getBadgeContainerBg( $item );
				$badge_in_style          = '';
				$badge_out_style         = '';

				if ( ! empty( $badge_position ) ) {
					$badge_out_style .= 'position: ' . $badge_position . ';';
				}
				if ( ! empty( $badge_y_position ) || ! empty( $badge_x_position ) ) {
					$badge_out_style .= 'transform: translate(' . $badge_x_position . ', ' . $badge_y_position . ');';
				}

				if ( ! empty( $badge_container_bg ) ) {
					$badge_in_style .= 'background-color: ' . $badge_container_bg . ';';
				}
				if ( ! empty( $badge_container_padding ) ) {
					$badge_in_style .= 'padding: ' . $badge_container_padding . ';';
				}
				if ( ! empty( $badge_container_radius ) ) {
					$badge_in_style .= 'border-radius: ' . $badge_container_radius . ';';
				}


				switch ( $badge_type ) {
					case 'image':
						$badge_image       = $this->getBadgeImage( $item );
						$badge_image_sizes = $this->getBadgeImageWidthHeight( $item );

						if ( ! empty( $badge_in_style ) ) {
							$badge_in_style = $attr . '="' . $badge_in_style . '" ';
						}

						if ( ! empty( $badge_image ) ) {
							$badge_html = '<span ' . $badge_in_style . '><img src="' . $this->getBadgeImage( $item ) . '" alt="" ' . $badge_image_sizes . '></span>';
						}
						break;

					case 'icon':
						$badge_icon       = $this->getBadgeIcon( $item );
						$badge_icon_size  = $this->getBadgeIconSize( $item );
						$badge_icon_color = $this->getBadgeIconColor( $item );

						if ( ! empty( $badge_icon_color ) ) {
							$badge_in_style .= 'color: ' . $badge_icon_color . ';';
						}
						if ( ! empty( $badge_icon_size ) ) {
							$badge_in_style .= 'font-size: ' . $badge_icon_size . 'px;';
						}

						if ( ! empty( $badge_icon ) ) {
							if ( ! empty( $badge_in_style ) ) {
								$badge_in_style = $attr . '="' . $badge_in_style . '" ';
							}
							$badge_html = '<span ' . $badge_in_style . '><i class="' . $badge_icon . '"></i></span>';
						}
						break;


					case 'text':
						$badge_text         = $this->getBadgeText( $item );
						$badge_text_family  = $this->getBadgeTextFontFamily( $item );
						$badge_text_variant = $this->getBadgeTextFontVariant( $item );
						$badge_text_size    = $this->getBadgeTextFontSize( $item );
						$badge_text_color   = $this->getBadgeTextFontColor( $item );
						if ( ! empty( $badge_text_family ) ) {
							$fontClass           = new GroovyMenuGFonts();
							$common_font_variant = $badge_text_variant;
							if ( 'inherit' === $common_font_variant ) {
								$common_font_variant = 'regular';
							}
							$fontClass->add_gfont_face_simple( $badge_text_family, $common_font_variant, true );

							$badge_in_style .= 'font-family: \'' . $badge_text_family . '\';';
						}
						if ( ! empty( $badge_text_variant ) ) {
							$common_font_variant = intval( $badge_text_variant );
							if ( empty( $common_font_variant ) || 'regular' === $badge_text_variant || 'italic' === $badge_text_variant ) {
								$common_font_variant = 400;
							}
							$badge_in_style .= 'font-weight: ' . $common_font_variant . ';';
							$pos             = strpos( $badge_text_variant, 'italic' );
							if ( false !== $pos ) {
								$badge_in_style .= 'font-style: italic;';
							}
						}
						if ( ! empty( $badge_text_color ) ) {
							$badge_in_style .= 'color: ' . $badge_text_color . ';';
						}
						if ( ! empty( $badge_text_size ) ) {
							$badge_in_style .= 'font-size: ' . $badge_text_size . 'px;';
						}

						if ( ! empty( $badge_text ) ) {
							if ( ! empty( $badge_in_style ) ) {
								$badge_in_style = $attr . '="' . $badge_in_style . '" ';
							}
							$badge_html = '<span ' . $badge_in_style . '>' . $badge_text . '</span>';
						}
						break;
				}


				if ( ! empty( $badge_out_style ) ) {
					$badge_out_style = $attr . '="' . $badge_out_style . '" ';
				}

				if ( ! empty( $badge_placement ) && ! empty( $badge_html ) ) {
					$badge[ $badge_placement ] .= '<span class="gm-badge" ' . $badge_out_style . '>' . $badge_html . '</span>';
				}
			}

			$current_title = apply_filters( 'the_title', $item->title, $item->ID );
			if ( in_array( $current_title, $hiding_symbol, true ) ) {
				$current_title = '';
			}

			$item_output .= '<span class="gm-menu-item__txt-wrapper">';
			$item_output .= $badge['left'];
			$item_output .= '<span class="gm-menu-item__txt' . ( empty( $item->url ) ? ' gm-menu-item__txt-empty-url' : '' ) . '">';
			$item_output .= $link_before . $current_title . $link_after;
			$item_output .= '</span>'; // .gm-menu-item__txt
			$item_output .= $badge['right'];
			$item_output .= '</span>'; // .gm-menu-item__txt-wrapper

			if ( ! empty( $item->url ) ) {
				$item_output .= '</a>';
			} else {
				$item_output .= '</div>';
			}

		} else {
			if ( $depth < 1 ) {
				if ( $this->hasParents() && $this->hasChildren( $classes ) ) {
					$item_output .= '<span class="gm-caret ' . $atts['class'] . '" aria-label="submenu"><i class="fa fa-fw fa-angle-right"></i></span>';
				} elseif ( $this->hasChildren( $classes ) ) {
					$item_output .= '<span class="gm-caret ' . $atts['class'] . '" aria-label="dropdown"><i class="fa fa-fw fa-angle-down"></i></span>';
				}
			}
		}
		$item_output .= $postContent;
		$item_output .= $item_after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}


	/**
	 * @param $classes
	 *
	 * @return bool
	 */
	protected function hasChildren( $classes ) {
		return in_array( 'menu-item-has-children', $classes, true );
	}


	/**
	 * @return bool
	 */
	protected function hasParents() {
		return $this->currentLvl > 0;
	}

}
