<?php foreach ($messages as $message) : ?>
  <div class="notice notice-<?php echo $message['type']; ?> is-dismissible"> 
    <p><?php echo $message['message']; ?></p>
  </div>
<?php endforeach; ?>