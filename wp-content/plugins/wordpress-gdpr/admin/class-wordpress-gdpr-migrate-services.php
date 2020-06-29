<?php

class WordPress_GDPR_Migrate_Services extends WordPress_GDPR
{
    protected $plugin_name;
    protected $version;
    protected $options;

    /**
     * Store Locator Plugin Construct
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @param   string                         $plugin_name
     * @param   string                         $version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    /**
     * Init the Public
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function init()
    {
        global $wordpress_gdpr_options;

        $this->options = $wordpress_gdpr_options;

        if (!$this->get_option('enable')) {
            return false;
        }

        return true;
    }

    public function check_action()
    {
    	if(!isset($_GET['wordpress_gdpr']) || !is_admin()) {
    		return false;
		}

		if(!isset($_GET['wordpress_gdpr']['migrate-services'])) {
			return false;
		}

        $terms = array(
            'necessary' => array(
                'name' => __('Necessary', 'wordpress-gdpr'),
                'args' => array(
                    'description'=> __('These cookies are necessary for the website to function and cannot be switched off in our systems.', 'wordpress-gdpr'),
                    'slug' => 'necessary',
                ),
            ),
            'preferences' => array(
                'name' => __('Preferences', 'wordpress-gdpr'),
                'args' => array(
                    'description'=> __('Preference cookies enable a website to remember information that changes the way the website behaves or looks, like your preferred language or the region that you are in.', 'wordpress-gdpr'),
                    'slug' => 'preferences',
                ),
            ),
            'analytics' => array(
                'name' => __('Analytics', 'wordpress-gdpr'),
                'args' => array(
                    'description'=> __('These cookies allow us to count visits and traffic sources, so we can measure and improve the performance of our site.', 'wordpress-gdpr'),
                    'slug' => 'analytics',
                ),
            ),
            'marketing' => array(
                'name' => __('Marketing', 'wordpress-gdpr'),
                'args' => array(
                    'description'=> __('These cookies are set through our site by our advertising partners.', 'wordpress-gdpr'),
                    'slug' => 'marketing',
                ),
            ),
            'unclassified' => array(
                'name' => __('Unclassified', 'wordpress-gdpr'),
                'args' => array(
                    'description'=> __('Unclassified cookies are cookies that we are in the process of classifying, together with the providers of individual cookies.', 'wordpress-gdpr'),
                    'slug' => 'unclassified',
                ),
            ),
        );

        $terms_created = array();
        foreach ($terms as $key => $term) {
            $term_exists = absint( term_exists($term['name']) );
            $terms_created[$key] = $term_exists;
            if(term_exists($term['name'])) {
                continue;
            }

            $term_inserted = wp_insert_term($term['name'], 'gdpr_service_categories', $term['args']);
            if(isset($term_inserted['term_id'])) {
                $terms_created[$key] = $term_inserted['term_id'];
            }
        }

        $options = get_option('wordpress_gdpr_options');
        $possibleServices = array(
            'integrationsCloudflare' => array (
                'newOption' => 0,
                'cookies' => '__cfduid',
                'deactivatable' => '0',
                'page' => array(
                    'post_title'    => __('Cloudflare', 'wordpress-gdpr'),
                    'post_content'  => __('For perfomance reasons we use Cloudflare as a CDN network. This saves a cookie "__cfduid" to apply security settings on a per-client basis. This cookie is strictly necessary for Cloudflare\'s security features and cannot be turned off.', 'wordpress-gdpr'),
                    'post_type' => 'gdpr_service',
                    'post_status'   => 'publish',
                ),
                'term' => 'necessary'
            ),
            'integrationsQuform' => array (
                'newOption' => 1,
                'cookies' => 'quform',
                'deactivatable' => '0',
                'page' => array(
                    'post_title'    => __('Quform', 'wordpress-gdpr'),
                    'post_content'  => __('We use Quform Plugin for all contact forms on our website. This stores a security token.', 'wordpress-gdpr'),
                    'post_type' => 'gdpr_service',
                    'post_status'   => 'publish',
                ),
                'term' => 'necessary'
            ),
            'integrationsWooCommerce' => array (
                'newOption' => 1,
                'cookies' => 'woocommerce_cart_hash,woocommerce_items_in_cart',
                'deactivatable' => '0',
                'page' => array(
                    'post_title'    => __('WooCommerce', 'wordpress-gdpr'),
                    'post_content'  => __('We use WooCommerce as a shopping system. For cart and order processing 2 cookies will be stored. This cookies are strictly necessary and can not be turned off.', 'wordpress-gdpr'),
                    'post_type' => 'gdpr_service',
                    'post_status'   => 'publish',
                ),
                'term' => 'necessary'
            ),
            'integrationsGoogleAnalytics' => array (
                'newOption' => 0,
                'cookies' => '_ga,_gid,_gat',
                'deactivatable' => '1',
                'page' => array(
                    'post_title'    => __('Google Analytics', 'wordpress-gdpr'),
                    'post_content'  => __('We track anonymized user information to improve our website.', 'wordpress-gdpr'),
                    'post_type' => 'gdpr_service',
                    'post_status'   => 'publish',
                ),
                'term' => 'analytics'
            ),
            'integrationsGoogleAdwords'  => array (
                'newOption' => 0,
                'cookies' => '',
                'deactivatable' => '1',
                'page' => array(
                    'post_title'    => __('Google Adwords', 'wordpress-gdpr'),
                    'post_content'  => __('We use Adwords to track our Conversions through Google Clicks.', 'wordpress-gdpr'),
                    'post_type' => 'gdpr_service',
                    'post_status'   => 'publish',
                ),
                'term' => 'marketing'
            ),
            'integrationsGoogleTagManager'  => array (
                'newOption' => 0,
                'cookies' => '',
                'deactivatable' => '1',
                'page' => array(
                    'post_title'    => __('Google Tag Manager', 'wordpress-gdpr'),
                    'post_content'  => __('We use Google Tag Manager to monitor our traffic and to help us AB test new features.', 'wordpress-gdpr'),
                    'post_type' => 'gdpr_service',
                    'post_status'   => 'publish',
                ),
                'term' => 'marketing'
            ),
            'integrationsHotJar'  => array (
                'newOption' => 0,
                'cookies' => '_hjClosedSurveyInvites,_hjDonePolls,_hjMinimizedPolls,_hjDoneTestersWidgets,_hjMinimizedTestersWidgets,_hjIncludedInSample,1P_JAR',
                'deactivatable' => '1',
                'page' => array(
                    'post_title'    => __('Hot Jar', 'wordpress-gdpr'),
                    'post_content'  => __('We use HotJar to track what users click on and engage with on this site', 'wordpress-gdpr'),
                    'post_type' => 'gdpr_service',
                    'post_status'   => 'publish',
                ),
                'term' => 'marketing'
            ),
            'integrationsFacebook'  => array (
                'newOption' => 0,
                'cookies' => 'm_pixel_ratio,presence,sb,wd,xs,fr,tr,c_user,datr',
                'deactivatable' => '1',
                'page' => array(
                    'post_title'    => __('Facebook Pixel', 'wordpress-gdpr'),
                    'post_content'  => __('We use Facebook to track connections to social media channels.', 'wordpress-gdpr'),
                    'post_type' => 'gdpr_service',
                    'post_status'   => 'publish',
                ),
                'term' => 'marketing'
            ),
            'integrationsPiwik'  => array (
                'newOption' => 0,
                'cookies' => '_pk_ref,_pk_cvar,_pk_id,_pk_ses,_pk_uid',
                'deactivatable' => '1',
                'page' => array(
                    'post_title'    => __('Piwik', 'wordpress-gdpr'),
                    'post_content'  => __('We use Piwik to track user information to improve our website.', 'wordpress-gdpr'),
                    'post_type' => 'gdpr_service',
                    'post_status'   => 'publish',
                ),
                'term' => 'analytics'
            ),
            'integrationsAdsense'  => array (
                'newOption' => 0,
                'cookies' => '_tlc,_tli,_tlp,_tlv,DSID,id,IDE',
                'deactivatable' => '1',
                'page' => array(
                    'post_title'    => __('Google Adsense', 'wordpress-gdpr'),
                    'post_content'  => __('We use Google AdSense to show online advertisements on our website.', 'wordpress-gdpr'),
                    'post_type' => 'gdpr_service',
                    'post_status'   => 'publish',
                ),
                'term' => 'marketing'
            ),
            'integrationsCustom'  => array (
                'newOption' => 0,
                'cookies' => '',
                'deactivatable' => '1',
                'page' => array(
                    'post_title'    => __('Custom', 'wordpress-gdpr'),
                    'post_content'  => __('Use Loco Translate to change Text for custom Code', 'wordpress-gdpr'),
                    'post_type' => 'gdpr_service',
                    'post_status'   => 'publish',
                ),
                'term' => 'unclassified'
            ),
        );

        if(!empty($this->get_option('customAllowedCookies'))) {
            $page = array(
                'post_title'    => __('Technical Cookies', 'wordpress-gdpr'),
                'post_content'  => __('In order to use this website we use the following technically required cookies', 'wordpress-gdpr'),
                'post_type' => 'gdpr_service',
                'post_status'   => 'publish',
            );

            $service_inserted = wp_insert_post($page);
            if(!$service_inserted) {
                wp_die('Could not insert service customCookies');
            }

            wp_set_post_terms($service_inserted, $terms_created['necessary'], 'gdpr_service_categories');
            update_post_meta($service_inserted, 'deactivatable', '0');
            update_post_meta($service_inserted, 'cookies', $this->get_option('customAllowedCookies'));

            $options['customAllowedCookies'] = '';

        };

        foreach ($possibleServices as $possibleService => $possibleServicePost) {
            if(!$this->get_option($possibleService) == "1") {
                continue;
            }
            
            $service_inserted = wp_insert_post($possibleServicePost['page']);

            if(!$service_inserted) {
                wp_die('Could not insert service' . $possibleService);
            }


            $headCode = $this->get_option($possibleService . 'Code');
            if(!empty($headCode)) {
                update_post_meta($service_inserted, 'head_script', $headCode);
            }

            $bodyCode = $this->get_option($possibleService . 'CodeBody');
            if(!empty($bodyCode)) {
                update_post_meta($service_inserted, 'body_script', $bodyCode);
            }
            wp_set_post_terms($service_inserted, $terms_created[$possibleServicePost['term']], 'gdpr_service_categories');
            update_post_meta($service_inserted, 'deactivatable', $possibleServicePost['deactivatable']);
            update_post_meta($service_inserted, 'cookies', $possibleServicePost['cookies']);

            $options[$possibleService] = $possibleServicePost['newOption'];

        }

        $customIntegrations = $this->get_option('integrationsCustoms');
        if(!empty($customIntegrations)) {
            foreach ($customIntegrations as $key => $customIntegration) {

                if(empty($customIntegration['title'])) {
                    continue;
                }

                $post = array(
                    'post_title'    => $customIntegration['title'],
                    'post_content'  => $customIntegration['url'],
                    'post_type' => 'gdpr_service',
                    'post_status'   => 'publish',
                );

                $service_inserted = wp_insert_post($post);
                if(!$service_inserted) {
                    wp_die('Could not insert service' . $possibleService);
                }

                $headCode = $customIntegration['description'];
                if(!empty($headCode)) {
                    update_post_meta($service_inserted, 'head_script', $headCode);
                }

                wp_set_post_terms($service_inserted, $terms_created['unclassified'], 'gdpr_service_categories');
                update_post_meta($service_inserted, 'deactivatable', '1');
                update_post_meta($service_inserted, 'cookies', '');
            }
            $options['integrationsCustoms'] = array();
        }

        update_option('wordpress_gdpr_options', $options);	
		wp_redirect( get_admin_url() . 'edit.php?post_type=gdpr_service' );
    }
}