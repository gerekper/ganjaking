<?php 
    $image_url = $image_id = '';
    if( isset( $value['url'] ) ) {
        $image_url = $value['url'];
    }
    if( isset( $value['id'] ) ) {
        $image_id = $value['id'];
    }
?>

<div class="betterdocs-media-field-wrapper">
    <div class="betterdocs-thumb-container <?php echo $image_url == '' ? '' : 'betterdocs-has-thumb'; ?>">
        <?php 
            if( $image_url ) {
                echo '<img src="'. esc_url( $image_url ) .'">';
            }
        ?>
    </div>
    <div class="betterdocs-media-content">
        <input class="betterdocs-media-url" type="text" name="<?php echo $name; ?>[url]" value="<?php echo esc_url( $image_url ); ?>">
        <input class="betterdocs-media-id" type="hidden" name="<?php echo $name; ?>[id]" value="<?php echo esc_attr( $image_id ); ?>">
        <button class="betterdocs-media-button betterdocs-media-remove-button <?php echo $image_url == '' ? 'hidden' : ''; ?>">Remove</button>
    </div>
    <button class="betterdocs-media-button betterdocs-media-upload-button <?php echo $image_url == '' ? '' : 'hidden'; ?>">Upload</button>
</div>