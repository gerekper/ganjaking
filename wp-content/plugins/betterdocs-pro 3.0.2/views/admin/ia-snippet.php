<div class="betterdocs-cross-domain-code">
    <div id="betterdocs-ia"></div>
    <link rel="stylesheet" href="<?php echo betterdocs_pro()->assets->asset_url( 'public/css/instant-answer.css' ); ?>">
    <style type="text/css"><?php echo $styles; ?></style>
    <script> window.betterdocs = <?php echo wp_json_encode( $scripts ); ?> </script>
    <script src="<?php echo betterdocs_pro()->assets->asset_url( 'public/js/instant-answer-cd.js' ); ?>"></script>
</div>
