<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
} ?>
<?php if (0 === $page) {
  $page = 1;
}
if (!isset($url)) {
  $url = add_query_arg(array());
}
if (EnergyPlus_Helpers::post('q')) {
  $url = add_query_arg(array('s'=>EnergyPlus_Helpers::post('q')), $url);
}
?>

<?php

$pages = paginate_links( array(
  'base' => remove_query_arg( 'pg', $url). '&pg=%#%',
  'format' => '?pg=%#%',
  'current' => max( 1, $page ),
  'total' => ceil($count/$per_page),
  'type'  => 'array',
  'prev_next'   => true,
  'prev_text'    => __('«'),
  'next_text'    => __('»'),
)
);

if( is_array( $pages ) ) {
  $paged = ( get_query_var('pg') == 0 ) ? 1 : get_query_var('pg');

  $pagination = '<nav aria-label="Page navigation"><ul class="__A__Pagination pagination-sm justify-content-center pagination">';

  foreach ( $pages as $page ) {
    $pagination .= "<li class='page-item'>".str_replace('page-numbers','page-link', $page)."</li>";
  }

  $pagination .= '</ul></nav>';

  echo wp_kses_post($pagination);

}
?>
