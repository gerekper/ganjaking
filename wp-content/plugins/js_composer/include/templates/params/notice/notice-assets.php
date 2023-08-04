<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<style>
	.wpb-notice.updated {
		position: relative;
		display:flex;
		gap: 20px;
		align-items: center;
		margin: 0;
		border-left-color: #3172A7;
		padding: 20px;
	}
	.wpb-notice-image > img {
		width: 100px;
		height: 100px;
	}
	.wpb-notice-text {
		color: #656565;
	}
	.wpb-notice .wpb-notice-text .title {
		font-size: 18px;
		font-weight: 500;
		margin: 0;
		padding: 0;
	}
	.wpb-notice .wpb-notice-text .wpb-notice-context {
		font-size: 16px;
		margin: 6px 0 14px 0;
	}
	.wpb-notice-text button {
		border: none;
		margin: 0;
		padding: 10px 15px;
		text-align: inherit;
		font: inherit;
		appearance: none;
		font-size: 16px;
		border-radius: 5px;
		cursor: pointer;
	}
</style>
<script>
	(function ( $ ) {
		var setCookie = function ( c_name, value, days ) {
			var date = new Date();
			date.setDate( date.getDate() + days );
			var c_value = encodeURIComponent( value ) + ((null === days) ? "" : "; expires=" + date.toUTCString());
			document.cookie = c_name + "=" + c_value;
		};

		$( document ).off( 'click.wpb-notice-dismiss' ).on( 'click.wpb-notice-dismiss', '.wpb-notice-dismiss', function ( e ) {
			e.preventDefault();
			var $el = jQuery( this ).closest(
				'.wpb-notice' );
			$el.fadeTo( 100, 0, function () {
				$el.slideUp( 100, function () {
					$el.remove();
				} );
			} );
			setCookie( $el.attr('id'), 1, 3000 );
		});
		$( document ).off( 'click.wpb-notice-button' ).on( 'click.wpb-notice-button', '.wpb-notice-button', function ( e ) {
			e.preventDefault();
			var $el = jQuery( this )

			var link = $el.attr('data-notice-link')			

			if ( link ) {
				window.open(link, '_blank')
			}
		});
		$( document ).off( 'click.wpb-notice-image' ).on( 'click.wpb-notice-image', '.wpb-notice-image', function ( e ) {
			e.preventDefault();
			var $el = jQuery( this )

			var link = $el.attr('data-notice-link')

			if ( link ) {
				window.open(link, '_blank')
			}
		});
	})( window.jQuery );
</script>
