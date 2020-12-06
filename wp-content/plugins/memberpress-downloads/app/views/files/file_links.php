<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<ul class="mpdl-file-links-list">
  <?php foreach($files as $file): ?>
    <li class="mpdl-file-links-item">
      <a <?php echo (isset($link_id)) ? "id=\"{$link_id}\"" : ''; ?> href="<?php echo $file->url(); ?>" class="<?php echo memberpress\downloads\helpers\Files::file_link_class($file->filetype); ?><?php echo (isset($link_class)) ? " {$link_class}" : ''; ?>"><?php echo $file->post_title; ?></a>
    </li>
  <?php endforeach; ?>
</ul>
