<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_view_back_mp3 extends WYSIJA_view_back{
    function __construct(){
        $this->skip_header =true;

    }

    function defaultDisplay(){
        $this->displayMP3();
    }

    function displayMP3() {
		?>

      <div class="wrap mpoet-wrap full-width-layout">
        <?php if(isset($_GET['arg']) && $_GET['arg'] === 'whats_new'): ?>
          <div class="changelog">
            <h2><?php echo __('Nice work, you’ve updated MailPoet 2!', WYSIJA ); ?></h2>
            <p><?php echo $this->replace_link_shortcode(__('Wondering what’s new? Check out [link]the change log[/link]', WYSIJA), 'https://wordpress.org/plugins/wysija-newsletters/#developers'); ?>
          </div>
          <hr>
        <?php endif; ?>
        <div class="feature-section one-col"><div class="col">
          <h2><?php echo __('Did you know we rebuilt MailPoet to help you send even better emails? Start using MailPoet 3 today!', WYSIJA); ?></h2>
        </div></div>
        <div class="full-width lead-description">
          <img src="<?php echo WYSIJA_URL ?>img/mailpoet3/top_image.png" width=900 height=500>
        </div>

        <div class="feature-section one-col" style="padding-bottom: 0"><div class="col center">
          <a class="button-primary" href="plugin-install.php?s=mailpoet&tab=search&type=author"><?php echo __('Install MailPoet 3 now', WYSIJA); ?></a>
          <p style="margin: 2em 0 2em 0">(<?php echo __('It installs as a separate plugins, so your existing MailPoet 2 plugin will continue to work', WYSIJA); ?>)
        </div></div>

        <hr style="margin-bottom: 2em">

        <div class="floating-header-section">
          <div class="section-header">
            <h2><?php echo __('A New But Familiar Experience', WYSIJA ); ?></h2>
            <img src="<?php echo WYSIJA_URL ?>img/mailpoet3/side_image_1.png" width=300 height=450>
          </div>
          <div class="section-content">
            <div class="section-item">
              <h3><?php echo __('All New Interface', WYSIJA); ?></h3>
              <p><?php echo __('We redesigned our drag-and-drop designer so it’s sleeker and more intuitive than ever. With over 20 templates to choose from (all free!), you’ll delight and impress subscribers when your emails land in their inboxes.', WYSIJA); ?>
            </div>
            <div class="section-item">
              <h3><?php echo __('All-New MailPoet Sending Service', WYSIJA); ?></h3>
              <p><?php echo __('We built our all-new MailPoet Sending Service specifically to handle the demands of WordPress. With more than 30+ million emails now passing through our servers each month, enjoy high open rates with our industry-leading 97.5% deliverability rate.', WYSIJA); ?>
            </div>
            <div class="section-item">
              <h3><?php echo __('We’re Phasing Out MailPoet 2', WYSIJA); ?></h3>
              <p><?php echo __('We’ve rebuilt it from the ground up to give you better performance and stability, a stunning new interface, all-new integration with the MailPoet Sending Service, and updated plans offering better value. On the other hand, the old MailPoet is only getting security updates.', WYSIJA); ?>
            </div>
            <div class="section-item">
              <h3><?php echo __('You Asked, We Built It!', WYSIJA); ?></h3>
              <p><?php echo __('MailPoet is a community-driven project, so when you ask, we listen and deliver. We’ve built your most highly requested features into MailPoet 3, including the ability to segment subscribers by opens or clicks (Premium), left/right post image alignment, and all—new templates.', WYSIJA); ?>
            </div>
            <div class="section-item">
              <h3><?php echo __('Send Welcome Emails for Free', WYSIJA); ?></h3>
              <p><?php echo __('Just like in MailPoet 2, set up emails to be sent automatically after one of your subscribers signs up. In MailPoet 3, this feature became Premium. We made it free again. Hurray, hurray.', WYSIJA); ?>
            </div>
          </div>
        </div>
        <div class="floating-header-section">
          <div class="section-header">
            <h2><?php echo __('Try Risk-Free', WYSIJA ); ?></h2>
              <img src="<?php echo WYSIJA_URL ?>img/mailpoet3/side_image_2.png" width=300 height=450>
          </div>
          <div class="section-content">
            <div class="section-item">
              <h3><?php echo __('Easy to Switch', WYSIJA); ?></h3>
              <p><?php echo __('With our handy migration tool, you can quickly copy over your subscribers, settings, and forms to MailPoet 3. You’ll need to recreate your email designs, but there’s no learning curve here. Just a friendly and familiar interface you know—only better.', WYSIJA); ?>
            </div>
            <div class="section-item">
              <h3><?php echo __('80,000 Users Can’t Be Wrong', WYSIJA); ?></h3>
              <p><?php echo __('Did we mention 80,000 users have already made the switch…? If you like using MailPoet 2, keep it—we’re maintaining it for security so you’ll always be safe. Just know that when you switch to MailPoet 3, you’ll enjoy regular updates, new features, and improvements into the future.', WYSIJA); ?>
            </div>
            <div class="section-item">
              <h3><?php echo __('Installs As a Separate Plugin', WYSIJA); ?></h3>
              <p><?php echo __('Curious but not ready to make the switch? MailPoet 3 installs as a separate plugin so you can use both versions at the same time since they don’t conflict with each other. So you can install and try MailPoet 3, see what it’s like, and keep your archive and stats in MailPoet 2.', WYSIJA); ?>
            </div>
            <div class="section-item">
              <h3><?php echo __('Need Help Switching?', WYSIJA); ?></h3>
              <p><?php echo __('We’re happy to chat with you! With team members spread around the world, MailPoet offers fast, reliable and friendly support via email. Prefer to DIY? Our knowledge base is always online. Reach out any time — we’re real humans and here to help.', WYSIJA); ?>
            </div>
          </div>
        </div>
        <div class="floating-header-section" style="margin-bottom: 0">
          <div class="section-header">
            <h2><?php echo __('Features Built for Tomorrow', WYSIJA ); ?></h2>
              <img src="<?php echo WYSIJA_URL ?>img/mailpoet3/side_image_3.png" width=300 height=450>
          </div>
          <div class="section-content">
            <div class="section-item">
              <h3><?php echo __('Updated Weekly', WYSIJA); ?></h3>
              <p><?php echo __('MailPoet employs a talented group of people who geek out on providing outstanding email delivery. No interns here — just real engineers applying 30 years of combined experience to deliver improvements, bug fixes, and features you ask for on a weekly release schedule.', WYSIJA); ?>
            </div>
            <div class="section-item">
              <h3><?php echo __('GDPR Compliant', WYSIJA); ?></h3>
              <p><?php echo __('We use MailPoet to run MailPoet, so we protect your data as if it were our own. In just a few clicks, you can create GDPR-friendly sign-up forms that help you comply with European privacy regulations.', WYSIJA); ?>
            </div>
          </div>
        </div>
        <div class="feature-section one-col"><div class="col center" style="margin-top: 0">
          <a class="button-primary" href="plugin-install.php?s=mailpoet&tab=search&type=author"><?php echo __('Install MailPoet 3 now', WYSIJA); ?></a>
          <p style="margin: 2em 0 2em 0">(<?php echo __('It installs as a separate plugins, so your existing MailPoet 2 plugin will continue to work', WYSIJA); ?>)
          <p style="margin: 2em 0 4em 0"><a href="https://demo.mailpoet.com" target="_blank"><?php echo __('Alternatively, try the demo (new tab).', WYSIJA) ?></a>
        </div></div>

        <hr>

        <div class="feature-section one-col"><div class="col">
          <h2><?php echo __('Watch the MailPoet 3 install video', WYSIJA); ?></h2>
        </div></div>

        <div class="feature-video">
          <iframe src="https://player.vimeo.com/video/295142268" width="640" height="360" frameborder="0"></iframe>
        </div>

        <hr>

        <div class="feature-section one-col"><div class="col">
          <h2><?php echo __('Is your site compatible with MailPoet 3?', WYSIJA); ?></h2>
          <ul style="max-width:500px;margin:auto;list-style:none;">
            <?php echo $this->displayPHPCompatibility(); ?>
            <?php echo $this->displayRtlCompatibility(); ?>
            <?php echo $this->displayMultisiteCompatibility(); ?>
            <?php echo $this->displayIISCompatibility(); ?>
          </ul>
        </div></div>

        <div class="feature-section one-col"><div class="col center">
          <a class="button-primary" href="plugin-install.php?s=mailpoet&tab=search&type=author"><?php echo __('Install MailPoet 3 now', WYSIJA); ?></a>
          <p style="margin: 2em 0 2em 0">(<?php echo __('It installs as a separate plugins, so your existing MailPoet 2 plugin will continue to work', WYSIJA); ?>)
          <p style="margin: 2em 0 4em 0"><a href="https://demo.mailpoet.com" target="_blank"><?php echo __('Alternatively, try the demo (new tab).', WYSIJA) ?></a>
        </div></div>

      </div>
<?php
    }

    private function replace_link_shortcode($text, $url) {
      $count = 1;
      return preg_replace(
        '/\[\/link\]/',
        '</a>',
        preg_replace(
          '/\[link\]/',
          sprintf('<a href="%s">', $url),
          $text,
          $count
        ),
        $count
      );
    }

    private function displayPHPCompatibility() {
      if(version_compare(phpversion(), '5.6.0', '<')) {
        return sprintf('<li class="red-cross">' . __('PHP version 5.6 or above (you have version %s)', WYSIJA), phpversion());
      } else {
        return sprintf('<li class="black-tick">' . __('PHP version 5.6 or above (you have version %s)', WYSIJA), phpversion());
      }
    }

    private function displayRtlCompatibility() {
      if(is_rtl()) {
        return sprintf('<li class="red-cross">' . __('We don’t officially support right-to-left language (it is still usable)', WYSIJA), phpversion());
      }
    }

  private function displayMultisiteCompatibility() {
    if(is_multisite()) {
      return sprintf('<li class="red-cross">' . __('We don’t officially support Multisite (it is still usable)', WYSIJA), phpversion());
    }
  }

  private function displayIISCompatibility() {
    if(isset($_SERVER['SERVER_SOFTWARE']) && strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'microsoft-iis') !== false) {
      return sprintf('<li class="red-cross">' . __('We don’t officially support IIS (you can still try)', WYSIJA), phpversion());
    }
  }
}
