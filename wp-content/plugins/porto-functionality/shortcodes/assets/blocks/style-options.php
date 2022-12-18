<?php

if ( ! empty( $settings['bg'] ) || ! empty( $settings['border'] ) || ! empty( $settings['padding'] ) || ! empty( $settings['margin'] ) || ! empty( $settings['position'] ) || ! empty( $settings['borderRadius'] ) || ! empty( $settings['transform'] ) || ! empty( $settings['boxshadow'] ) ) {
	echo '.page-wrapper ' . sanitize_text_field( $settings['selector'] ) . '{';
	if ( ! empty( $settings['bg'] ) ) {
		if ( ! empty( $settings['bg']['color'] ) ) {
			echo 'background-color:' . sanitize_text_field( $settings['bg']['color'] ) . ';';
		}
		if ( ! empty( $settings['bg']['img_url'] ) ) {
			echo 'background-image:url(' . esc_url( $settings['bg']['img_url'] ) . ');';
		}
		if ( ! empty( $settings['bg']['position'] ) ) {
			echo 'background-position:' . sanitize_text_field( $settings['bg']['position'] ) . ';';
		}
		if ( ! empty( $settings['bg']['attachment'] ) ) {
			echo 'background-attachment:' . sanitize_text_field( $settings['bg']['attachment'] ) . ';';
		}
		if ( ! empty( $settings['bg']['repeat'] ) ) {
			echo 'background-repeat:' . sanitize_text_field( $settings['bg']['repeat'] ) . ';';
		}
		if ( ! empty( $settings['bg']['size'] ) ) {
			echo 'background-size:' . sanitize_text_field( $settings['bg']['size'] ) . ';';
		}
	}

	if ( ! empty( $settings['border'] ) ) {
		if ( ! empty( $settings['border']['style'] ) ) {
			echo 'border-style:' . sanitize_text_field( $settings['border']['style'] ) . ';';
		}
		if ( isset( $settings['border']['top'], $settings['border']['right'], $settings['border']['bottom'], $settings['border']['left'] ) && strlen( $settings['border']['top'] ) && strlen( $settings['border']['right'] ) && strlen( $settings['border']['bottom'] ) && strlen( $settings['border']['left'] ) ) {
			echo 'border-width:' . sanitize_text_field( $settings['border']['top'] . ' ' . $settings['border']['right'] . ' ' . $settings['border']['bottom'] . ' ' . $settings['border']['left'] ) . ';';
		} else {
			if ( isset( $settings['border']['top'] ) && strlen( $settings['border']['top'] ) ) {
				echo 'border-top-width:' . sanitize_text_field( $settings['border']['top'] ) . ';';
			}
			if ( isset( $settings['border']['right'] ) && strlen( $settings['border']['right'] ) ) {
				echo 'border-right-width:' . sanitize_text_field( $settings['border']['right'] ) . ';';
			}
			if ( isset( $settings['border']['bottom'] ) && strlen( $settings['border']['bottom'] ) ) {
				echo 'border-bottom-width:' . sanitize_text_field( $settings['border']['bottom'] ) . ';';
			}
			if ( isset( $settings['border']['left'] ) && strlen( $settings['border']['left'] ) ) {
				echo 'border-left-width:' . sanitize_text_field( $settings['border']['left'] ) . ';';
			}
		}
		if ( ! empty( $settings['border']['color'] ) ) {
			echo 'border-color:' . sanitize_text_field( $settings['border']['color'] ) . ';';
		}
	}

	if ( ! empty( $settings['borderRadius'] ) ) {
		if ( isset( $settings['borderRadius']['top'], $settings['borderRadius']['right'], $settings['borderRadius']['bottom'], $settings['borderRadius']['left'] ) && strlen( $settings['borderRadius']['top'] ) && strlen( $settings['borderRadius']['right'] ) && strlen( $settings['borderRadius']['bottom'] ) && strlen( $settings['borderRadius']['left'] ) ) {
			echo 'border-radius:' . sanitize_text_field( $settings['borderRadius']['top'] . ' ' . $settings['borderRadius']['right'] . ' ' . $settings['borderRadius']['bottom'] . ' ' . $settings['borderRadius']['left'] ) . ';';
		} else {
			if ( isset( $settings['borderRadius']['top'] ) && strlen( str_replace( array( 'px', '%', 'em', 'rem', 'vw', 'vh' ), '', $settings['borderRadius']['top'] ) ) ) {
				echo 'border-top-left-radius:' . sanitize_text_field( $settings['borderRadius']['top'] ) . ';';
			}
			if ( isset( $settings['borderRadius']['right'] ) && strlen( str_replace( array( 'px', '%', 'em', 'rem', 'vw', 'vh' ), '', $settings['borderRadius']['right'] ) ) ) {
				echo 'border-top-right-radius:' . sanitize_text_field( $settings['borderRadius']['right'] ) . ';';
			}
			if ( isset( $settings['borderRadius']['bottom'] ) && strlen( str_replace( array( 'px', '%', 'em', 'rem', 'vw', 'vh' ), '', $settings['borderRadius']['bottom'] ) ) ) {
				echo 'border-bottom-right-radius:' . sanitize_text_field( $settings['borderRadius']['bottom'] ) . ';';
			}
			if ( isset( $settings['borderRadius']['left'] ) && strlen( str_replace( array( 'px', '%', 'em', 'rem', 'vw', 'vh' ), '', $settings['borderRadius']['left'] ) ) ) {
				echo 'border-bottom-left-radius:' . sanitize_text_field( $settings['borderRadius']['left'] ) . ';';
			}
		}
	}

	if ( ! empty( $settings['margin'] ) ) {
		if ( isset( $settings['margin']['top'] ) && isset( $settings['margin']['right'] ) && isset( $settings['margin']['bottom'] ) && isset( $settings['margin']['left'] ) && strlen( $settings['margin']['top'] ) && strlen( $settings['margin']['right'] ) && strlen( $settings['margin']['bottom'] ) && strlen( $settings['margin']['left'] ) ) {
			echo 'margin:' . sanitize_text_field( $settings['margin']['top'] . ' ' . $settings['margin']['right'] . ' ' . $settings['margin']['bottom'] . ' ' . $settings['margin']['left'] ) . ';';
		} else {
			if ( isset( $settings['margin']['top'] ) && strlen( $settings['margin']['top'] ) ) {
				echo 'margin-top:' . sanitize_text_field( $settings['margin']['top'] ) . ';';
			}
			if ( isset( $settings['margin']['right'] ) && strlen( $settings['margin']['right'] ) ) {
				echo 'margin-right:' . sanitize_text_field( $settings['margin']['right'] ) . ';';
			}
			if ( isset( $settings['margin']['bottom'] ) && strlen( $settings['margin']['bottom'] ) ) {
				echo 'margin-bottom:' . sanitize_text_field( $settings['margin']['bottom'] ) . ';';
			}
			if ( isset( $settings['margin']['left'] ) && strlen( $settings['margin']['left'] ) ) {
				echo 'margin-left:' . sanitize_text_field( $settings['margin']['left'] ) . ';';
			}
		}
	}

	if ( ! empty( $settings['padding'] ) ) {
		if ( isset( $settings['padding']['top'] ) && isset( $settings['padding']['right'] ) && isset( $settings['padding']['bottom'] ) && isset( $settings['padding']['left'] ) && strlen( $settings['padding']['top'] ) && strlen( $settings['padding']['right'] ) && strlen( $settings['padding']['bottom'] ) && strlen( $settings['padding']['left'] ) ) {
			echo 'padding:' . sanitize_text_field( $settings['padding']['top'] . ' ' . $settings['padding']['right'] . ' ' . $settings['padding']['bottom'] . ' ' . $settings['padding']['left'] ) . ';';
		} else {
			if ( isset( $settings['padding']['top'] ) && strlen( $settings['padding']['top'] ) ) {
				echo 'padding-top:' . sanitize_text_field( $settings['padding']['top'] ) . ';';
			}
			if ( isset( $settings['padding']['right'] ) && strlen( $settings['padding']['right'] ) ) {
				echo 'padding-right:' . sanitize_text_field( $settings['padding']['right'] ) . ';';
			}
			if ( isset( $settings['padding']['bottom'] ) && strlen( $settings['padding']['bottom'] ) ) {
				echo 'padding-bottom:' . sanitize_text_field( $settings['padding']['bottom'] ) . ';';
			}
			if ( isset( $settings['padding']['left'] ) && strlen( $settings['padding']['left'] ) ) {
				echo 'padding-left:' . sanitize_text_field( $settings['padding']['left'] ) . ';';
			}
		}
	}

	if ( ! empty( $settings['position'] ) ) {
		if ( ! empty( $settings['position']['style'] ) ) {
			echo 'position:' . sanitize_text_field( $settings['position']['style'] ) . ';';
		}
		if ( isset( $settings['position']['zindex'] ) && strlen( $settings['position']['zindex'] ) ) {
			echo 'z-index:' . sanitize_text_field( $settings['position']['zindex'] ) . ';';
		}
		if ( isset( $settings['position']['top'] ) && strlen( str_replace( array( 'px', '%', 'em', 'rem', 'vw', 'vh' ), '', $settings['position']['top'] ) ) ) {
			echo 'top:' . sanitize_text_field( $settings['position']['top'] ) . ';';
		}
		if ( isset( $settings['position']['right'] ) && strlen( str_replace( array( 'px', '%', 'em', 'rem', 'vw', 'vh' ), '', $settings['position']['right'] ) ) ) {
			echo 'right:' . sanitize_text_field( $settings['position']['right'] ) . ';';
		}
		if ( isset( $settings['position']['bottom'] ) && strlen( str_replace( array( 'px', '%', 'em', 'rem', 'vw', 'vh' ), '', $settings['position']['bottom'] ) ) ) {
			echo 'bottom:' . sanitize_text_field( $settings['position']['bottom'] ) . ';';
		}
		if ( isset( $settings['position']['left'] ) && strlen( str_replace( array( 'px', '%', 'em', 'rem', 'vw', 'vh' ), '', $settings['position']['left'] ) ) ) {
			echo 'left:' . sanitize_text_field( $settings['position']['left'] ) . ';';
		}
		if ( isset( $settings['position']['width'] ) && strlen( $settings['position']['width'] ) && 'custom' != $settings['position']['width'] ) {
			echo 'width:' . sanitize_text_field( $settings['position']['width'] ) . ';';
		}
		if ( isset( $settings['position']['width_val'] ) && strlen( str_replace( array( 'px', '%', 'em', 'rem', 'vw', 'vh' ), '', $settings['position']['width_val'] ) ) ) {
			echo 'width:' . sanitize_text_field( $settings['position']['width_val'] ) . ';';
		}
		if ( isset( $settings['position']['opacity'] ) && strlen( $settings['position']['opacity'] ) ) {
			echo 'opacity:' . sanitize_text_field( $settings['position']['opacity'] ) . ';';
		}

		if ( ( isset( $settings['position']['translatex'] ) && strlen( $settings['position']['translatex'] ) ) || ( isset( $settings['position']['translatey'] ) && strlen( $settings['position']['translatey'] ) ) ) {
			echo 'transform:';
			if ( isset( $settings['position']['translatex'] ) && strlen( $settings['position']['translatex'] ) ) {
				echo ' translateX(' . sanitize_text_field( $settings['position']['translatex'] ) . ')';
			}
			if ( isset( $settings['position']['translatey'] ) && strlen( $settings['position']['translatey'] ) ) {
				echo ' translateY(' . sanitize_text_field( $settings['position']['translatey'] ) . ')';
			}
			echo ';';
		}
	}

	if ( ! empty( $settings['transform'] ) ) {
		$transform_css = '';
		if ( ! empty( $settings['transform']['translate'] ) ) {
			if ( ! empty( $settings['transform']['translatex'] ) && ! empty( $settings['transform']['translatey'] ) ) {
				$transform_css .= ' translate(' . esc_html( $settings['transform']['translatex'] ) . ', ' . esc_html( $settings['transform']['translatey'] ) . ')';
			} elseif ( ! empty( $settings['transform']['translatex'] ) ) {
				$transform_css .= ' translateX(' . esc_html( $settings['transform']['translatex'] ) . ')';
			} elseif ( ! empty( $settings['transform']['translatey'] ) ) {
				$transform_css .= ' translateY(' . esc_html( $settings['transform']['translatey'] ) . ')';
			}
		}
		if ( ! empty( $settings['transform']['rotate'] ) && ! empty( $settings['transform']['rotatedeg'] ) ) {
			$transform_css .= ' rotate(' . esc_html( $settings['transform']['rotatedeg'] ) . 'deg)';
		}
		if ( ! empty( $settings['transform']['scale'] ) || ! empty( $settings['transform']['flipx'] ) || ! empty( $settings['transform']['flipy'] ) ) {
			if ( ! empty( $settings['transform']['scalex'] ) ) {
				$scaleX = (float) $settings['transform']['scalex'];
			}
			if ( ! empty( $settings['transform']['scaley'] ) ) {
				$scaleY = (float) $settings['transform']['scaley'];
			}

			if ( ! empty( $settings['transform']['flipx'] ) ) {
				if ( ! empty( $scaleX ) ) {
					$scaleX = -1 * $scaleX;
				} else {
					$scaleX = -1;
				}
			}
			if ( ! empty( $settings['transform']['flipy'] ) ) {
				if ( ! empty( $scaleY ) ) {
					$scaleY = -1 * $scaleY;
				} else {
					$scaleY = -1;
				}
			}
			if ( isset( $scaleX ) && isset( $scaleY ) ) {
				$transform_css .= ' scale(' . $scaleX . ', ' . $scaleY . ')';
			} elseif ( isset( $scaleX ) ) {
				$transform_css .= ' scaleX(' . $scaleX . ')';
			} elseif ( isset( $scaleY ) ) {
				$transform_css .= ' scaleY(' . $scaleY . ')';
			}
		}
		if ( ! empty( $settings['transform']['skew'] ) ) {
			if ( ! empty( $settings['transform']['skewx'] ) && ! empty( $settings['transform']['skewy'] ) ) {
				$transform_css .= ' skew(' . esc_html( $settings['transform']['skewx'] ) . 'deg, ' . esc_html( $settings['transform']['skewy'] ) . 'deg)';
			} elseif ( ! empty( $settings['transform']['skewx'] ) ) {
				$transform_css .= ' skewX(' . esc_html( $settings['transform']['skewx'] ) . 'deg)';
			} elseif ( ! empty( $settings['transform']['skewy'] ) ) {
				$transform_css .= ' skewY(' . esc_html( $settings['transform']['skewy'] ) . 'deg)';
			}
		}
		if ( $transform_css ) {
			echo 'transform:' . $transform_css . ';';
		}
		if ( ! empty( $settings['transform']['duration'] ) ) {
			echo 'transition:' . esc_html( $settings['transform']['duration'] ) . 'ms;';
		}
	}

	if ( ! empty( $settings['boxshadow'] ) && ( ! empty( $settings['boxshadow']['type'] ) || ! empty( $settings['boxshadow']['color'] ) ) ) {
		echo 'box-shadow:';
		if ( ! empty( $settings['boxshadow']['type'] ) && 'inset' != $settings['boxshadow']['type'] ) {
			echo sanitize_text_field( $settings['boxshadow']['type'] );
		} else {
			if ( ! empty( $settings['boxshadow']['type'] ) ) {
				echo sanitize_text_field( $settings['boxshadow']['type'] );
			}
			if ( ! empty( $settings['boxshadow']['x'] ) ) {
				echo ' ' . sanitize_text_field( $settings['boxshadow']['x'] );
			} else {
				echo ' 0';
			}
			if ( ! empty( $settings['boxshadow']['y'] ) ) {
				echo ' ' . sanitize_text_field( $settings['boxshadow']['y'] );
			} else {
				echo ' 0';
			}
			if ( ! empty( $settings['boxshadow']['blur'] ) ) {
				echo ' ' . sanitize_text_field( $settings['boxshadow']['blur'] );
			}
			if ( ! empty( $settings['boxshadow']['spread'] ) ) {
				echo ' ' . sanitize_text_field( $settings['boxshadow']['spread'] );
			}
			if ( ! empty( $settings['boxshadow']['color'] ) ) {
				echo ' ' . sanitize_text_field( $settings['boxshadow']['color'] );
			}
		}
		echo ';';
	}
	echo '}';
}

/* hover style */
if ( ! empty( $settings['hover'] ) ) {

	echo '.page-wrapper ' . sanitize_text_field( $settings['selector'] ) . ':hover{';
	if ( ! empty( $settings['hover']['bg'] ) ) {
		echo 'background-color:' . sanitize_text_field( $settings['hover']['bg'] ) . ';';
	}
	if ( ! empty( $settings['hover']['color'] ) ) {
		echo 'color:' . sanitize_text_field( $settings['hover']['color'] ) . ';';
	}
	if ( ! empty( $settings['hover']['border_style'] ) ) {
		echo 'border-style:' . sanitize_text_field( $settings['hover']['border_style'] ) . ';';
	}
	if ( isset( $settings['hover']['border_top'], $settings['hover']['border_right'], $settings['hover']['border_bottom'], $settings['hover']['border_left'] ) && strlen( $settings['hover']['border_top'] ) && strlen( $settings['hover']['border_right'] ) && strlen( $settings['hover']['border_bottom'] ) && strlen( $settings['hover']['border_left'] ) ) {
		echo 'border-width:' . sanitize_text_field( $settings['hover']['border_top'] . ' ' . $settings['hover']['border_right'] . ' ' . $settings['hover']['border_bottom'] . ' ' . $settings['hover']['border_left'] ) . ';';
	} else {
		if ( isset( $settings['hover']['border_top'] ) && strlen( $settings['hover']['border_top'] ) ) {
			echo 'border-top-width:' . sanitize_text_field( $settings['hover']['border_top'] ) . ';';
		}
		if ( isset( $settings['hover']['border_right'] ) && strlen( $settings['hover']['border_right'] ) ) {
			echo 'border-right-width:' . sanitize_text_field( $settings['hover']['border_right'] ) . ';';
		}
		if ( isset( $settings['hover']['border_bottom'] ) && strlen( $settings['hover']['bottom'] ) ) {
			echo 'border-bottom-width:' . sanitize_text_field( $settings['hover']['bottom'] ) . ';';
		}
		if ( isset( $settings['hover']['border_left'] ) && strlen( $settings['hover']['border_left'] ) ) {
			echo 'border-left-width:' . sanitize_text_field( $settings['hover']['border_left'] ) . ';';
		}
	}
	if ( ! empty( $settings['hover']['border_color'] ) ) {
		echo 'border-color:' . sanitize_text_field( $settings['hover']['border_color'] ) . ';';
	}
	if ( ! empty( $settings['hover']['top'] ) ) {
		echo 'top:' . sanitize_text_field( $settings['hover']['top'] ) . ';';
	}
	if ( ! empty( $settings['hover']['right'] ) ) {
		echo 'right:' . sanitize_text_field( $settings['hover']['right'] ) . ';';
	}
	if ( ! empty( $settings['hover']['bottom'] ) ) {
		echo 'bottom:' . sanitize_text_field( $settings['hover']['bottom'] ) . ';';
	}
	if ( ! empty( $settings['hover']['left'] ) ) {
		echo 'left:' . sanitize_text_field( $settings['hover']['left'] ) . ';';
	}
	if ( isset( $settings['hover']['opacity'] ) && strlen( $settings['hover']['opacity'] ) ) {
		echo 'opacity:' . floatval( $settings['hover']['opacity'] ) . ';';
	}
	if ( ( isset( $settings['hover']['translatex'] ) && strlen( $settings['hover']['translatex'] ) ) || ( isset( $settings['hover']['translatey'] ) && strlen( $settings['hover']['translatey'] ) ) ) {
		echo 'transform:';
		if ( isset( $settings['hover']['translatex'] ) && strlen( $settings['hover']['translatex'] ) ) {
			echo ' translateX(' . $settings['hover']['translatex'] . ')';
		}
		if ( isset( $settings['hover']['translatey'] ) && strlen( $settings['hover']['translatey'] ) ) {
			echo ' translateY(' . $settings['hover']['translatey'] . ')';
		}
		echo ';';
	}

	if ( ! empty( $settings['hover']['transform'] ) ) {
		$transform_css = '';
		if ( ! empty( $settings['hover']['transform']['translate'] ) ) {
			if ( ! empty( $settings['hover']['transform']['translatex'] ) && ! empty( $settings['hover']['transform']['translatey'] ) ) {
				$transform_css .= ' translate(' . esc_html( $settings['hover']['transform']['translatex'] ) . ', ' . esc_html( $settings['hover']['transform']['translatey'] ) . ')';
			} elseif ( ! empty( $settings['hover']['transform']['translatex'] ) ) {
				$transform_css .= ' translateX(' . esc_html( $settings['hover']['transform']['translatex'] ) . ')';
			} elseif ( ! empty( $settings['hover']['transform']['translatey'] ) ) {
				$transform_css .= ' translateY(' . esc_html( $settings['hover']['transform']['translatey'] ) . ')';
			}
		}
		if ( ! empty( $settings['hover']['transform']['rotate'] ) && ! empty( $settings['hover']['transform']['rotatedeg'] ) ) {
			$transform_css .= ' rotate(' . esc_html( $settings['hover']['transform']['rotatedeg'] ) . 'deg)';
		}
		if ( ! empty( $settings['hover']['transform']['scale'] ) || ! empty( $settings['hover']['transform']['flipx'] ) || ! empty( $settings['hover']['transform']['flipy'] ) ) {
			if ( ! empty( $settings['hover']['transform']['scalex'] ) ) {
				$scaleX = (float) $settings['hover']['transform']['scalex'];
			}
			if ( ! empty( $settings['hover']['transform']['scaley'] ) ) {
				$scaleY = (float) $settings['hover']['transform']['scaley'];
			}

			if ( ! empty( $settings['hover']['transform']['flipx'] ) ) {
				if ( ! empty( $scaleX ) ) {
					$scaleX = -1 * $scaleX;
				} else {
					$scaleX = -1;
				}
			}
			if ( ! empty( $settings['hover']['transform']['flipy'] ) ) {
				if ( ! empty( $scaleY ) ) {
					$scaleY = -1 * $scaleY;
				} else {
					$scaleY = -1;
				}
			}
			if ( isset( $scaleX ) && isset( $scaleY ) ) {
				$transform_css .= ' scale(' . $scaleX . ', ' . $scaleY . ')';
			} elseif ( isset( $scaleX ) ) {
				$transform_css .= ' scaleX(' . $scaleX . ')';
			} elseif ( isset( $scaleY ) ) {
				$transform_css .= ' scaleY(' . $scaleY . ')';
			}
		}
		if ( ! empty( $settings['hover']['transform']['skew'] ) ) {
			if ( ! empty( $settings['hover']['transform']['skewx'] ) && ! empty( $settings['hover']['transform']['skewy'] ) ) {
				$transform_css .= ' skew(' . esc_html( $settings['hover']['transform']['skewx'] ) . 'deg, ' . esc_html( $settings['hover']['transform']['skewy'] ) . 'deg)';
			} elseif ( ! empty( $settings['hover']['transform']['skewx'] ) ) {
				$transform_css .= ' skewX(' . esc_html( $settings['hover']['transform']['skewx'] ) . 'deg)';
			} elseif ( ! empty( $settings['hover']['transform']['skewy'] ) ) {
				$transform_css .= ' skewY(' . esc_html( $settings['hover']['transform']['skewy'] ) . 'deg)';
			}
		}
		if ( $transform_css ) {
			echo 'transform:' . $transform_css . ';';
		}
	}

	if ( ! empty( $settings['hover']['boxshadow'] ) && ( ! empty( $settings['hover']['boxshadow']['type'] ) || ! empty( $settings['hover']['boxshadow']['color'] ) ) ) {
		echo 'box-shadow:';
		if ( ! empty( $settings['hover']['boxshadow']['type'] ) && 'inset' != $settings['hover']['boxshadow']['type'] ) {
			echo sanitize_text_field( $settings['hover']['boxshadow']['type'] );
		} else {
			if ( ! empty( $settings['hover']['boxshadow']['type'] ) ) {
				echo sanitize_text_field( $settings['hover']['boxshadow']['type'] );
			}
			if ( ! empty( $settings['hover']['boxshadow']['x'] ) ) {
				echo ' ' . sanitize_text_field( $settings['hover']['boxshadow']['x'] );
			} else {
				echo ' 0';
			}
			if ( ! empty( $settings['hover']['boxshadow']['y'] ) ) {
				echo ' ' . sanitize_text_field( $settings['hover']['boxshadow']['y'] );
			} else {
				echo ' 0';
			}
			if ( ! empty( $settings['hover']['boxshadow']['blur'] ) ) {
				echo ' ' . sanitize_text_field( $settings['hover']['boxshadow']['blur'] );
			}
			if ( ! empty( $settings['hover']['boxshadow']['spread'] ) ) {
				echo ' ' . sanitize_text_field( $settings['hover']['boxshadow']['spread'] );
			}
			if ( ! empty( $settings['hover']['boxshadow']['color'] ) ) {
				echo ' ' . sanitize_text_field( $settings['hover']['boxshadow']['color'] );
			}
		}
		echo ';';
	}
	echo '}';
}
