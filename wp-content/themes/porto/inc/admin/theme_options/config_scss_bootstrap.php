<?php
	global $porto_settings;
	$porto_settings_backup = $porto_settings;
	$b                     = porto_check_theme_options();
	$porto_settings        = $porto_settings_backup;
	$dark                  = 'dark' == $b['css-type'];

	if ( (int) $b['container-width'] >= 1360 ) {
		$xl  = 1140;
		$xxl = (int) $b['container-width'];
	} else {
		$xl  = (int) $b['container-width'];
		$xxl = 1360;
	}
?>

$grid-breakpoints: (
  xs: 0,
  sm: 576px,
  md: 768px,
  lg: 992px,
  xl: <?php echo (int) $xl + (int) $b['grid-gutter-width']; ?>px,
  xxl: <?php echo (int) $xxl + (int) $b['grid-gutter-width'] * 2; ?>px
) !default;

$container-max-widths: (
  sm: 540px,
  md: 720px,
  lg: 960px,
  xl: <?php echo (int) $xl; ?>px,
  xxl: <?php echo (int) $b['container-width']; ?>px
) !default;

$grid-gutter-width:           <?php echo (int) $b['grid-gutter-width']; ?>px !default;


$primary:       <?php echo esc_html( $b['skin-color'] ); ?> !default;
$secondary:     <?php echo esc_html( $b['secondary-color'] ); ?> !default;
$success:       #47a447 !default;
$info:          #5bc0de !default;
$warning:       #ed9c28 !default;
$danger:        #d2322d !default;

<?php if ( $dark ) : ?>
	$dark: <?php echo esc_html( $b['color-dark'] ); ?>;
<?php else : ?>
	$dark: #1d2127;
<?php endif; ?>

$color-dark-1: $dark;
$color-dark-2: lighten($color-dark-1, 2%);
$color-dark-3: lighten($color-dark-1, 5%);
$color-dark-4: lighten($color-dark-1, 8%);
$color-dark-5: lighten($color-dark-1, 3%);
$color-darken-1: darken($color-dark-1, 2%);
$dark-bg: $dark;
$dark-default-text: #808697;

$link-decoration: none;

<?php if ( isset( $porto_settings['body-font'] ) && ! empty( $porto_settings['body-font']['font-family'] ) ) : ?>
	$font-family-base: <?php echo esc_html( $porto_settings['body-font']['font-family'] ); ?>, sans-serif;
<?php endif; ?>
<?php if ( $b['body-font']['font-weight'] && ( 400 !== (int) $b['body-font']['font-weight'] ) ) : ?>
	$font-weight-base: <?php echo esc_html( $b['body-font']['font-weight'] ); ?>;
<?php endif; ?>
<?php if ( $b['body-font']['font-size'] ) : ?>
	$font-size-base: <?php echo esc_html( $b['body-font']['font-size'] ); ?>;
<?php endif; ?>
<?php if ( $b['body-font']['line-height'] ) : ?>
	$line-height-base: <?php echo esc_html( $b['body-font']['line-height'] ); ?>;
<?php endif; ?>
$body-color: <?php echo isset( $b['body-font'], $b['body-font-color'] ) ? esc_html( $b['body-font-color'] ) : '#777'; ?>;

<?php if ( $dark ) : ?>
	$body-bg: #1d2127 !default;

	// Colors
	$gray-base:              #fff !default;
	$gray-darker:            #999 !default;
	$gray-dark:              #777 !default;
	$gray:                   #777 !default;
	$gray-light:             #777 !default;
	$gray-lighter:           $color-dark-4 !default;

	// Components
	$component-active-color:    $color-dark-3 !default;
	$component-active-bg:       $primary !default;

	// Tables
	$table-color:                   $gray-darker !default;
	$table-striped-bg:              $color-dark-3 !default;
	$table-striped-color:           $gray-darker !default;
	$table-hover-bg:                $color-dark-2 !default;
	$table-active-bg:               $table-hover-bg !default;
	$table-border-color:            $color-dark-3 !default;

	// Forms
	$input-bg:                       $color-dark-3 !default;
	$input-disabled-bg:              $gray-lighter !default;
	$input-color:                    #777 !default;
	$input-border-color:             $color-dark-3 !default;
	$input-focus-border-color:       $color-dark-4 !default;
	$input-placeholder-color:        #999 !default;
	$form-check-input-border:        1px solid rgba(#fff, .25) !default;
	$form-switch-color:              rgba(#fff, .25) !default;
	$form-range-thumb-box-shadow:    0 .1rem .25rem rgba(#fff, .1) !default;

	// Dropdowns
	$dropdown-bg:                    $color-dark-3 !default;
	$dropdown-border-color:          $color-dark-3 !default;
	$dropdown-divider-bg:            $color-dark-4 !default;
	$dropdown-link-color:            $gray-dark !default;
	$dropdown-link-hover-color:      darken($gray-dark, 5%) !default;
	$dropdown-link-hover-bg:         $color-dark-4 !default;

	// Tabs
	$nav-tabs-border-color:                     $color-dark-3 !default;
	$nav-tabs-link-active-border-color:			$color-dark-3 !default;
	$nav-tabs-link-hover-border-color:          $color-dark-3 !default;

	// Pagination
	$pagination-bg:                        $color-dark-3 !default;
	$pagination-border-color:              $color-dark-3 !default;
	$pagination-hover-border:              $color-dark-3 !default;
	$pagination-active-color:              $color-dark-3 !default;
	$pagination-disabled-bg:               $color-dark-3 !default;
	$pagination-disabled-border-color:     $color-dark-3 !default;

	// Popovers
	$popover-bg:                          $color-dark-3 !default;
	$popover-border-color:                $color-dark-3 !default;

	// Modals
	$modal-content-bg:						$color-dark-3 !default;
	$modal-content-border-color:			$color-dark-3 !default;
	$modal-backdrop-bg:						#fff !default;
	$modal-header-border-color:				$color-dark-3 !default;

	// Progress bars
	$progress-bg:                 $color-dark-4 !default;

	// List group
	$list-group-bg:                 $color-dark-3 !default;
	$list-group-border-color:       $color-dark-3 !default;
	$list-group-hover-bg:           $color-dark-4 !default;
	$list-group-action-color:       #bbb !default;
	$list-group-action-active-color:#ddd !default;

	// Cards
	$card-bg: $color-dark-3 !default;
	$card-border-color: $color-dark-3 !default;
	$card-cap-bg: $color-dark-4 !default;
	$card-border-color:          $color-dark-3 !default;

	//  Code
	$code-bg:                     $color-dark-3 !default;
	$kbd-color:                   #000 !default;
	$kbd-bg:                      #ccc !default;
<?php else : ?>
	$body-bg: #fff !default;
	// Colors
	$gray-base:              #000 !default;
	$gray-darker:            lighten($gray-base, 13.5%) !default; // #222
	$gray-dark:              lighten($gray-base, 20%) !default;   // #333
	$gray:                   lighten($gray-base, 33.5%) !default; // #555
	$gray-light:             lighten($gray-base, 46.7%) !default; // #777
	$gray-lighter:           lighten($gray-base, 93.5%) !default; // #eee

	// Tables
	$table-striped-bg:              #f9f9f9 !default;
	$table-hover-bg:                #f5f5f5 !default;
	$table-active-bg:               $table-hover-bg !default;
	$table-border-color:            #ddd !default;

	// Forms
	$input-disabled-bg:              $gray-lighter !default;
	$input-color:                    #777 !default;
	$input-border-color:             #ccc !default;
	$input-focus-border-color:       #66afe9 !default;
	$input-placeholder-color:        #999 !default;

	// Dropdowns
	$dropdown-divider-bg:            #e5e5e5 !default;
	$dropdown-link-color:            $gray-dark !default;
	$dropdown-link-hover-color:      darken($gray-dark, 5%) !default;
	$dropdown-link-hover-bg:         #f5f5f5 !default;

	// Tabs
	$nav-tabs-border-color:                     #e7e7e7 !default;
	$nav-tabs-link-active-border-color:			#e7e7e7 !default;
	$nav-tabs-link-hover-border-color:          #e7e7e7 !default;

	// Pagination
	$pagination-border-color:              #ddd !default;
	$pagination-hover-border:              #ddd !default;
	$pagination-active-color:              #fff !default;
	$pagination-disabled-bg:               #fff !default;
	$pagination-disabled-border-color:     #ddd !default;

	// Popovers
	$popover-border-color:					rgba(0,0,0,.2) !default;

	// Modals
	$modal-content-border-color:			rgba(0,0,0,.2) !default;
	$modal-header-border-color:				#e5e5e5 !default;

	// Progress bars
	$progress-bg:					#f5f5f5 !default;

	// List group
	$list-group-border-color:       #ddd !default;
	$list-group-hover-bg:           #f5f5f5 !default;
	$list-group-action-color:       #555 !default;
	$list-group-action-active-color:#333 !default;


<?php endif; ?>

<?php if ( $b['border-radius'] ) : ?>
	$border-radius:               .25rem !default;
	$border-radius-sm:            .2rem !default;
	$border-radius-lg:            .3rem !default;
<?php else : ?>
	$border-radius:               0 !default;
	$border-radius-sm:            0 !default;
	$border-radius-lg:            0 !default;
<?php endif; ?>

// Image thumbnails
<?php if ( $b['thumb-padding'] ) : ?>
	$thumbnail-padding:                 .25rem !default;
	$thumbnail-bg:                      $body-bg !default;
	$thumbnail-border-width:            1px !default;
	$thumbnail-border-color:            <?php echo ! $dark ? '#ddd' : '$color-dark-3'; ?> !default;
<?php else : ?>
	$thumbnail-padding:                 0 !default;
	$thumbnail-bg:                      $body-bg !default;
	$thumbnail-border-width:            0 !default;
	$thumbnail-border-color:            transparent !default;
<?php endif; ?>
