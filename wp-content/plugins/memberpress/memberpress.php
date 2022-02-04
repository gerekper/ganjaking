<?php
/*
Plugin Name: MemberPress Basic
Plugin URI: http://www.memberpress.com/
Description: The membership plugin that makes it easy to accept payments for access to your content and digital products.
Version: 1.9.28
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress
Copyright: 2004-2021, Caseproof, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
Also add information on how to contact you by electronic and paper mail.
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

define('MEPR_PLUGIN_SLUG','memberpress/memberpress.php');
define('MEPR_PLUGIN_NAME','memberpress');
define('MEPR_PATH',WP_PLUGIN_DIR.'/'.MEPR_PLUGIN_NAME);
define('MEPR_IMAGES_PATH',MEPR_PATH.'/images');
define('MEPR_CSS_PATH',MEPR_PATH.'/css');
define('MEPR_JS_PATH',MEPR_PATH.'/js');
define('MEPR_I18N_PATH',MEPR_PATH.'/i18n');
define('MEPR_LIB_PATH',MEPR_PATH.'/app/lib');
define('MEPR_INTEGRATIONS_PATH',MEPR_PATH.'/app/integrations');
define('MEPR_INTERFACES_PATH',MEPR_PATH.'/app/lib/interfaces');
define('MEPR_DATA_PATH',MEPR_PATH.'/app/data');
define('MEPR_VENDOR_LIB_PATH',MEPR_PATH.'/vendor/lib');
define('MEPR_APIS_PATH',MEPR_PATH.'/app/apis');
define('MEPR_MODELS_PATH',MEPR_PATH.'/app/models');
define('MEPR_CTRLS_PATH',MEPR_PATH.'/app/controllers');
define('MEPR_GATEWAYS_PATH',MEPR_PATH.'/app/gateways');
define('MEPR_EMAILS_PATH',MEPR_PATH.'/app/emails');
define('MEPR_JOBS_PATH',MEPR_PATH.'/app/jobs');
define('MEPR_VIEWS_PATH',MEPR_PATH.'/app/views');
define('MEPR_WIDGETS_PATH',MEPR_PATH.'/app/widgets');
define('MEPR_HELPERS_PATH',MEPR_PATH.'/app/helpers');

// Make all of our URLS protocol agnostic
$mepr_url_protocol = (is_ssl())?'https':'http'; //Can't use MeprUtils::is_ssl() here
define('MEPR_URL',preg_replace('/^https?:/', "{$mepr_url_protocol}:", plugins_url('/'.MEPR_PLUGIN_NAME)));

define('MEPR_VIEWS_URL',MEPR_URL.'/app/views');
define('MEPR_IMAGES_URL',MEPR_URL.'/images');
define('MEPR_CSS_URL',MEPR_URL.'/css');
define('MEPR_JS_URL',MEPR_URL.'/js');
define('MEPR_GATEWAYS_URL',MEPR_URL.'/app/gateways');
define('MEPR_VENDOR_LIB_URL',MEPR_URL.'/vendor/lib');
define('MEPR_SCRIPT_URL',site_url('/index.php?plugin=mepr'));
define('MEPR_OPTIONS_SLUG', 'mepr_options');
define('MEPR_EDITION', 'memberpress-basic');

define('MEPR_MIN_PHP_VERSION', '5.6.20');

update_option( 'mepr_activated', 1 );

$mepr_options = get_option( 'mepr_options' );
if ( empty( $mepr_options) || empty( $mepr_options['mothership_license'] ) ) {
    $mepr_options['mothership_license'] = '********-****-****-****-************';
    update_option( 'mepr_options', $mepr_options );
}

set_site_transient( 'mepr_license_info', [
    'license_key' => [
        'id' => 99999,
        'license' => '********-****-****-****-************',
        'status' => 'enabled',
        'user_id' => 99999,
        'product_id' => 99,
        'created_at' => '2021-01-01T00:00:00.000Z',
        'updated_at' => '2021-01-01T00:00:00.000Z',
        'expires_at' => '2030-01-01T00:00:00.000Z',
        'addon' => false,
        'subscription' => 'mp-sub-99999',
    ],
    'product_name' => 'MemberPress Pro',
    'product_slug' => 'memberpress-pro',
    'user' => [
        'id' => 99999,
        'email' => 'email@email.com',
        'uuid' => '********-****-****-****-************',
        'status' => 'enabled',
        'created_at' => '2021-01-01T00:00:00.000Z',
        'updated_at' => '2021-01-01T00:00:00.000Z'
    ],
    'activation_count' => 1,
    'max_activations' => 999,
    'extra_info' => [
        'main_file' => 'memberpress/memberpress.php',
        'directory' => 'memberpress',
        'description' => 'MemberPress is the WordPress Membership Plugin of Champions',
    ]
] );

set_site_transient( 'mepr_all_addons', json_encode( [
    'memberpress-activecampaign' => [
        'product_name' => 'MemberPress ActiveCampaign - Lists Version',
        'product_slug' => 'memberpress-activecampaign',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-activecampaign/main.php',
            'directory' => 'memberpress-activecampaign',
            'description' => 'ActiveCampaign (http://www.activecampaign.com/) autoresponder integration. Lists Version.',
            'list_title' => 'ActiveCampaign (Lists Version)',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-activecampaign.png'
        ]
    ],
    'memberpress-activecampaign-tags' => [
        'product_name' => 'MemberPress ActiveCampaign - Tags Version',
        'product_slug' => 'memberpress-activecampaign-tags',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-activecampaign-tags/main.php',
            'directory' => 'memberpress-activecampaign-tags',
            'description' => 'ActiveCampaign (http://www.activecampaign.com/) autoresponder integration. Tags Version.',
            'list_title' => 'ActiveCampaign (Tags Version)',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-activecampaign.png'
        ]
    ],
    'memberpress-aweber' => [
        'product_name' => 'MemberPress AWeber',
        'product_slug' => 'memberpress-aweber',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-aweber/main.php',
            'directory' => 'memberpress-aweber',
            'description' => 'AWeber (http://www.aweber.com/) autoresponder integration',
            'list_title' => 'AWeber',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-aweber.png'
        ]
    ],
    'memberpress-aws' => [
        'product_name' => 'MemberPress AWS',
        'product_slug' => 'memberpress-aws',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-aws/memberpress-aws.php',
            'directory' => 'memberpress-aws',
            'description' => 'Allows you you protect and embed expiring links, audio & video to content you have stored on Amazon S3',
            'list_title' => 'Amazon Web Services (AWS)',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-aws.png'
        ]
    ],
    'memberpress-beaver-builder' => [
        'product_name' => 'MemberPress Beaver Builder',
        'product_slug' => 'memberpress-beaver-builder',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-beaver-builder/memberpress-beaver-builder.php',
            'directory' => 'memberpress-beaver-builder',
            'description' => 'Beaver Builder integration for MemberPress',
            'list_title' => 'Beaver Builder',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-beaver-builder.png'
        ]
    ],
    'memberpress-buddypress' => [
        'product_name' => 'MemberPress BuddyPress',
        'product_slug' => 'memberpress-buddypress',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-buddypress/main.php',
            'directory' => 'memberpress-buddypress',
            'description' => 'BuddyPress (https://buddypress.org/) integration for MemberPress',
            'list_title' => 'BuddyPress Integration',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-buddypress.png'
        ]
    ],
    'memberpress-constantcontact' => [
        'product_name' => 'MemberPress Constant Contact',
        'product_slug' => 'memberpress-constantcontact',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-constantcontact/main.php',
            'directory' => 'memberpress-constantcontact',
            'description' => 'Constant Contact (http://www.constantcontact.com/) autoresponder integration',
            'list_title' => 'Constant Contact',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-constantcontact.png'
        ]
    ],
    'memberpress-convertkit' => [
        'product_name' => 'MemberPress ConvertKit',
        'product_slug' => 'memberpress-convertkit',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-convertkit/main.php',
            'directory' => 'memberpress-convertkit',
            'description' => 'ConvertKit (http://convertkit.com) autoresponder integration',
            'list_title' => 'ConvertKit',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-convertkit.png'
        ]
    ],
    'memberpress-corporate' => [
        'product_name' => 'MemberPress Corporate Accounts',
        'product_slug' => 'memberpress-corporate',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-corporate/main.php',
            'directory' => 'memberpress-corporate',
            'description' => 'Corporate (aka Group, Parent or Umbrella) Accounts for MemberPress',
            'list_title' => 'Corporate Accounts',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-corporate.png'
        ]
    ],
    'memberpress-courses' => [
        'product_name' => 'MemberPress Courses',
        'product_slug' => 'memberpress-courses',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-courses/main.php',
            'directory' => 'memberpress-courses',
            'description' => 'Get the ease of use you expect from MemberPress combined with powerful LMS features designed to make building online courses simple.',
            'list_title' => 'Courses',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-courses.png'
        ]
    ],
    'memberpress-developer-tools' => [
        'product_name' => 'MemberPress Developer Tools',
        'product_slug' => 'memberpress-developer-tools',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-developer-tools/main.php',
            'directory' => 'memberpress-developer-tools',
            'description' => 'Adds MemberPress webhooks for events and a Remote API with dynamic, in-plugin documentation',
            'list_title' => 'Developer Tools',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-developer-tools.png'
        ]
    ],
    'memberpress-divi' => [
        'product_name' => 'MemberPress Divi',
        'product_slug' => 'memberpress-divi',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-divi/memberpress-divi.php',
            'directory' => 'memberpress-divi',
            'description' => 'Divi integration for MemberPress',
            'list_title' => 'Divi',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-divi.png'
        ]
    ],
    'memberpress-downloads' => [
        'product_name' => 'MemberPress Downloads',
        'product_slug' => 'memberpress-downloads',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-downloads/main.php',
            'directory' => 'memberpress-downloads',
            'description' => 'Upload and control access to files for your memberships in MemberPress.',
            'list_title' => 'Downloads',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-downloads.png'
        ]
    ],
    'memberpress-drip-tags' => [
        'product_name' => 'MemberPress Drip - Tags',
        'product_slug' => 'memberpress-drip-tags',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-drip-tags/main.php',
            'directory' => 'memberpress-drip-tags',
            'description' => 'Drip (https://www.getdrip.com/) autoresponder integration - Tags Version',
            'list_title' => 'Drip - Tags Version',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-drip-tags.png'
        ]
    ],
    'memberpress-elementor' => [
        'product_name' => 'MemberPress Elementor',
        'product_slug' => 'memberpress-elementor',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-elementor/memberpress-elementor.php',
            'directory' => 'memberpress-elementor',
            'description' => 'Elementor integration for MemberPress',
            'list_title' => 'Elementor',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-elementor.png'
        ]
    ],
    'memberpress-getresponse' => [
        'product_name' => 'MemberPress GetResponse',
        'product_slug' => 'memberpress-getresponse',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-getresponse/main.php',
            'directory' => 'memberpress-getresponse',
            'description' => 'GetResponse (http://www.getresponse.com/) autoresponder integration',
            'list_title' => 'GetResponse',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-getresponse.png'
        ]
    ],
    'memberpress-gifting' => [
        'product_name' => 'MemberPress Gifting',
        'product_slug' => 'memberpress-gifting',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-gifting/memberpress-gifting.php',
            'directory' => 'memberpress-gifting',
            'description' => 'Allow your memberships to be gifted',
            'list_title' => 'Gifting',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-gifting.png'
        ]
    ],
    'memberpress-helpscout' => [
        'product_name' => 'MemberPress HelpScout',
        'product_slug' => 'memberpress-helpscout',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-helpscout/main.php',
            'directory' => 'memberpress-helpscout',
            'description' => 'HelpScout (http://helpscout.com) Custom App Integration',
            'list_title' => 'HelpScout',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-helpscout.png'
        ]
    ],
    'memberpress-importer' => [
        'product_name' => 'MemberPress Importer',
        'product_slug' => 'memberpress-importer',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-importer/memberpress-importer.php',
            'directory' => 'memberpress-importer',
            'description' => 'Tools to Import data into MemberPress.',
            'list_title' => 'Importer',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-importer.png'
        ]
    ],
    'memberpress-mailchimp-tags' => [
        'product_name' => 'MemberPress MailChimp 3.0',
        'product_slug' => 'memberpress-mailchimp-tags',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-mailchimp-tags/main.php',
            'directory' => 'memberpress-mailchimp-tags',
            'description' => 'MailChimp (http://mailchimp.com/) 3.0 autoresponder integration. Uses one list with Merge Tags for each Membership level.',
            'list_title' => 'MailChimp 3.0',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-mailchimp.png'
        ]
    ],
    'memberpress-mailpoet' => [
        'product_name' => 'MemberPress MailPoet',
        'product_slug' => 'memberpress-mailpoet',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-mailpoet/main.php',
            'directory' => 'memberpress-mailpoet',
            'description' => 'MailPoet (https://www.mailpoet.com/) autoresponder integration',
            'list_title' => 'MailPoet',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-mailpoet.png'
        ]
    ],
    'memberpress-mailster' => [
        'product_name' => 'MemberPress Mailster',
        'product_slug' => 'memberpress-mailster',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-mailster/main.php',
            'directory' => 'memberpress-mailster',
            'description' => 'Mailster (https://mailster.co/) autoresponder integration',
            'list_title' => 'Mailster',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-mailster.png'
        ]
    ],
    'memberpress-math-captcha' => [
        'product_name' => 'MemberPress Math Captcha',
        'product_slug' => 'memberpress-math-captcha',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-math-captcha/main.php',
            'directory' => 'memberpress-math-captcha',
            'description' => 'Adds a simple math Captcha to the signup process',
            'list_title' => 'Math Captcha',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-math-captcha.png'
        ]
    ],
    'memberpress-pdf-invoice' => [
        'product_name' => 'MemberPress PDF Invoice',
        'product_slug' => 'memberpress-pdf-invoice',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-pdf-invoice/memberpress-pdf-invoice.php',
            'directory' => 'memberpress-pdf-invoice',
            'description' => 'PDF Invoice Downloads for MemberPress',
            'list_title' => 'PDF Invoice',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-downloads.png'
        ]
    ],
    'memberpress-wpbakery' => [
        'product_name' => 'MemberPress WPBakery',
        'product_slug' => 'memberpress-wpbakery',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'memberpress-wpbakery/memberpress-wpbakery.php',
            'directory' => 'memberpress-wpbakery',
            'description' => 'WPBakery integration for MemberPress',
            'list_title' => 'WPBakery',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/memberpress-wpbakery.png'
        ]
    ],
    'affiliate-royale-mp' => [
        'product_name' => 'Affiliate Royale MemberPress Edition',
        'product_slug' => 'affiliate-royale-mp',
        'installable' => true,
        'extra_info' => [
            'main_file' => 'affiliate-royale/affiliate-royale.php',
            'directory' => 'affiliate-royale',
            'description' => 'A full-featured Affiliate Program WordPress Plugin that works seamlessly with MemberPress (https://www.affiliateroyale.com/)',
            'list_title' => 'Affiliate Royale',
            'cover_image' => 'https://mepr-add-on-icons.s3.amazonaws.com/400x400/affiliate-royale.png'
        ]
    ]
] ) );

/**
 * Returns current plugin version.
 *
 * @return string Plugin version
 */
function mepr_plugin_info($field) {
  static $curr_plugins;

  if( !isset($curr_plugins) ) {
    if( !function_exists( 'get_plugins' ) ) {
      require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
    }

    $curr_plugins = get_plugins();
    wp_cache_delete('plugins', 'plugins');
  }

  if(isset($curr_plugins[MEPR_PLUGIN_SLUG][$field])) {
    return $curr_plugins[MEPR_PLUGIN_SLUG][$field];
  }

  return '';
}

// Plugin Information from the plugin header declaration
define('MEPR_VERSION', mepr_plugin_info('Version'));
define('MEPR_DISPLAY_NAME', mepr_plugin_info('Name'));
define('MEPR_AUTHOR', mepr_plugin_info('Author'));
define('MEPR_AUTHOR_URI', mepr_plugin_info('AuthorURI'));
define('MEPR_DESCRIPTION', mepr_plugin_info('Description'));

// Autoload all the requisite classes
function mepr_autoloader($class_name)
{
  // Only load MemberPress classes here
  if(preg_match('/^Mepr.+$/', $class_name))
  {
    if(preg_match('/^.+Interface$/', $class_name)) // Load interfaces first
      $filepath = MEPR_INTERFACES_PATH."/{$class_name}.php";
    else if(preg_match('/^Mepr(Base|Cpt).+$/', $class_name)) // Base classes are in lib
      $filepath = MEPR_LIB_PATH."/{$class_name}.php";
    else if(preg_match('/^.+Ctrl$/', $class_name))
      $filepath = MEPR_CTRLS_PATH."/{$class_name}.php";
    else if(preg_match('/^.+Helper$/', $class_name))
      $filepath = MEPR_HELPERS_PATH."/{$class_name}.php";
    else if(preg_match('/^.+Exception$/', $class_name))
      $filepath = MEPR_LIB_PATH."/MeprExceptions.php";
    else if(preg_match('/^.+Jobs$/', $class_name))
      $filepath = MEPR_LIB_PATH."/MeprJobs.php";
    else if(preg_match('/^.+Gateway$/', $class_name)) {
      foreach( MeprGatewayFactory::paths() as $path ) {
        $filepath = $path."/{$class_name}.php";
        if( file_exists($filepath) ) {
          require_once($filepath); return;
        }
      }
      return;
    }
    else if(preg_match('/^.+Email$/', $class_name)) {
      foreach( MeprEmailFactory::paths() as $path ) {
        $filepath = $path."/{$class_name}.php";
        if( file_exists($filepath) ) {
          require_once($filepath); return;
        }
      }
      return;
    }
    else if(preg_match('/^.+Job$/', $class_name)) {
      foreach( MeprJobFactory::paths() as $path ) {
        $filepath = $path."/{$class_name}.php";
        if( file_exists($filepath) ) {
          require_once($filepath); return;
        }
      }
      return;
    }
    else {
      $filepath = MEPR_MODELS_PATH."/{$class_name}.php";

      // Now let's try the lib dir if its not a model
      if(!file_exists($filepath))
        $filepath = MEPR_LIB_PATH."/{$class_name}.php";
    }

    if(file_exists($filepath))
      require_once($filepath);
  }
}

// if __autoload is active, put it on the spl_autoload stack
if(is_array(spl_autoload_functions()) and in_array('__autoload', spl_autoload_functions()))
  spl_autoload_register('__autoload');

// Add the autoloader
spl_autoload_register('mepr_autoloader');

// Load integration files
foreach ( (array) glob( MEPR_INTEGRATIONS_PATH . '/*/Integration.php' ) as $file ) {
  include_once $file;
}

// Load our controllers
MeprCtrlFactory::all();

// Setup screens
MeprAppCtrl::setup_menus();

// Start Job Processor / Scheduler
new MeprJobs();

// Template Tags
function mepr_account_link() {
  try {
    $account_ctrl = MeprCtrlFactory::fetch('account');
    echo $account_ctrl->get_account_links();
  }
  catch(Exception $e) {
    // Silently fail ... not much we can do if the account controller isn't present
  }
}

register_activation_hook( MEPR_PLUGIN_SLUG, function() { require_once( MEPR_LIB_PATH . "/activation.php"); });
register_deactivation_hook( MEPR_PLUGIN_SLUG, function() { require_once( MEPR_LIB_PATH . "/deactivation.php"); });
