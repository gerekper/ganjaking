<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<div>
  <input type="hidden" name="mpdl-file-nonce" value="<?php echo wp_create_nonce('mpdl-file-nonce' . wp_salt()); ?>" />
  <input type="hidden" id="mpdl-file-name" name="mpdl-file-name" value="<?php echo (isset($file)) ? $file->filename : ''; ?>" data-validation="required" data-validation-error-msg-container="#file-upload-notice p" data-validation-error-msg="<?php _e('You cannot publish without uploading a file', 'memberpress-downloads'); ?>" />
  <input type="hidden" id="mpdl-file-size" name="mpdl-file-size" value="<?php echo (isset($file)) ? $file->filesize : ''; ?>" />
  <input type="hidden" id="mpdl-file-type" name="mpdl-file-type" value="<?php echo (isset($file)) ? $file->filetype : ''; ?>" />

  <!-- File details section -->
  <div id="file-details-container" class="content<?php echo (empty($file->filename)) ? ' hidden' : '' ?>">
    <div id="flexbox-container">
      <div id="file-details">
        <div id="file-name"><?php echo $file->filename; ?></div>
        <div id="file-size">&#40;<?php echo memberpress\downloads\helpers\Files::human_filesize($file->filesize); ?>&#41;</div>
        <div id="file-thumb">
        <?php if(\preg_match('/image\/\w+/', $file->filetype)): ?>
          <img src="<?php echo $file->thumb_url(); ?>">
        <?php else: ?>
          <i class="<?php echo memberpress\downloads\helpers\Files::file_thumb($file->filetype); ?> mpdl-icon large"></i>
        <?php endif; ?>
        <div><a id="mpdl-replace-file" href="#" class="button"><?php _e('Replace File', 'memberpress-downloads'); ?></a></div>
        </div>
      </div>
    </div>
  </div>
  <!-- end File details section -->
  <!-- File upload section -->
  <div id="upload-file" class="content<?php echo (empty($file->filename)) ? '' : ' hidden' ?>">
    <div id="drop-zone" class="drop-zone">
      <h2><?php _e('Drop file here to upload', 'memberpress-downloads'); ?></h2>
      <div><?php _e('or', 'memberpress-downloads'); ?></div>
      <div>
        <label for="mpdl-file-upload" class="button"><span><?php _e('Select File', 'memberpress-downloads'); ?></span></label>
        <input id="mpdl-file-upload" type="file" name="mpdl-file-upload" data-url="<?php echo admin_url('admin-ajax.php'); ?>">
      </div>
      <?php if(!empty($file->filename)): ?>
        <div><a id="mpdl-cancel-upload" href="#"><?php _e('Cancel', 'memberpress-downloads'); ?></a></div>
      <?php endif; ?>
    </div>
    <p class="max-upload-size"><?php printf(__('Maximum upload file size: %s.', 'memberpress-downloads'), esc_html(size_format($max_upload_size))); ?></p>
    <div id="file-upload-notice" class="file-upload-notice hidden">
      <p><!-- Error messages will display here --></p>
    </div>
    <?php if(isset($_GET['mpdl-warn'])): ?>
      <div class="notice notice-warning is-dismissible">
        <p>
          <?php _e('Due to an issue with your web host configuration this file will not be downloadable until you fix your web host configuration.', 'memberpress-downloads'); ?>
        </p>
      </div>
    <?php endif; ?>
  </div>
  <!-- end File upload section -->
  <!-- Upload progress section -->
  <div id="upload-progress" class="content hidden">
    <div><?php _e('Uploading file...', 'memberpress-downloads'); ?></div>
    <div id="uploading-filename"><p>{Filename}</p></div>
    <div id="progress">
      <div class="bar"></div>
    </div>
  </div>
  <!-- end Upload progress section -->
</div>
