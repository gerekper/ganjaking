<?php

$content = wpautop( htmlspecialchars_decode( str_replace( '\\','', $content ) )  );
$content = apply_filters( 'the_content', $content );
?>

<div class="tab-editor-container ywtm_content_tab"> <?php echo do_shortcode( $content ); ?></div>