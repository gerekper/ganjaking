<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !empty($FilterType) && ($FilterType == 'search_list') || $paginationType == 'ajaxbased') { ?>
    <div class="tp-skeleton">  
        <div class="tp-skeleton-img loading">
            <div class="tp-skeleton-bottom">
                <div class="tp-skeleton-title loading"></div>
                <div class="tp-skeleton-description loading"></div>
            </div>
        </div>
    </div>
<?php } ?>