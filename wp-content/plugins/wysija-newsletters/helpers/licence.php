<?php
defined('WYSIJA') or die('Restricted access');
/**
 * class managing the admin vital part to integrate wordpress
 */
class WYSIJA_help_licence extends WYSIJA_help{

    function __construct(){
        parent::__construct();
    }

    /**
     *
     * @param string $source
     * @return string
     */
    function get_url_checkout($source = 'not_specified', $campaign = 'wpadmin'){
        $helper_toolbox = WYSIJA::get('toolbox' , 'helper');
        $currency = 'USD';
        if($helper_toolbox->is_european()){
            $currency = 'EUR';
        }
        return 'http://www.mailpoet.com/checkout/?wysijadomain='.$this->getDomainInfo(true).'&nc=1&utm_medium=plugin&utm_campaign='.$campaign.'&utm_source='.$source.'&currency='.$currency;
    }

    /**
     *
     * @param boolean $checkout_data
     * @return string
     */
    function getDomainInfo($checkout_data = false){
        $domain_data = array();

        $url = admin_url('admin.php');

        $helper_toolbox = WYSIJA::get('toolbox','helper');
        $domain_data['domain_name'] = $helper_toolbox->_make_domain_name($url);

        if(is_multisite()) {
            $domain_data['multisite_domain'] = $helper_toolbox->_make_domain_name(network_site_url());
        }
        $domain_data['url'] = $url;
        $domain_data['cron_url'] = site_url( 'wp-cron.php').'?'.WYSIJA_CRON.'&action=wysija_cron&process=all&silent=1';

        if($checkout_data){
            $model_config = WYSIJA::get('config' , 'model');
            if(!$model_config->getValue('poll_origin')){
                $domain_data['poll_origin'] = 'unidentified';
            }else{
                $domain_data['poll_origin'] = $model_config->getValue('poll_origin');
                $domain_data['poll_origin_url'] = $model_config->getValue('poll_origin_url');
            }

            $domain_data['installed_time'] = $model_config->getValue('installed_time');

        }

        return base64_encode(serialize($domain_data));
    }

    function check($js = false){

        $domain_data = $this->getDomainInfo();

        if($js === false) {
            WYSIJA::update_option('wysijey', $domain_data);
        }

        $response=array();
        $response['domain_name'] = $domain_data;
        $response['nocontact'] = false;

        $model_config=WYSIJA::get('config','model');
        if($model_config->getValue('nocurl')){
            $json_result=false;
        }else{
            $helper_http = WYSIJA::get('http','helper');
            $json_result = $helper_http->wp_request('http://www.mailpoet.com/?wysijap=checkout&wysijashop-page=1&controller=customer&action=checkDomain&data='.urlencode($domain_data));
        }

        if($json_result!==false) {
            $decoded = json_decode($json_result, true);

            if(isset($decoded['result']) === false) {
                // service unavailable
                $response['nocontact'] = true;
                // make sure to re check later
                WYSIJA::update_option('wysicheck', true);
            } else {
                // set result
                $response['result'] = $decoded['result'];

                if($decoded['result'] === true) {
                    // set premium key
                    $config_data = array(
                        'premium_key' => base64_encode(get_option('home').time()),
                        'premium_val' => time(),
                        'premium_expire_at' => (int) $decoded['expire_at']
                    );

                    // success message
                    $this->notice(__('Premium version is valid for your site.', WYSIJA));

                    // stop checking premium
                    WYSIJA::update_option('wysicheck', false);
                } else {
                    // set error

                    if(isset($decoded['error'])) {
                        $response['code'] = $decoded['code'];
                        switch($response['code']){
                            case 1: //Domain \'%1$s\' does not exist.
                                //$error_msg=__('\'%1$s\' does not exist!',WYSIJA);

                                $error_msg=__('Your website doesn\'t seem to have a license! Log into your [link]account manager[/link] to add a license for this website.',WYSIJA);
                                break;
                            case 2: //'Licence (id: %d) does not exist for domain "%s"
                                $error_msg=__('There\'s no license for "%1$s". If you\'re Premium, add this domain in your [link]account manager[/link].',WYSIJA);
                                break;
                            case 3: //Licence has expired

                                $renew_url = 'http://www.mailpoet.com/checkout/?utm_medium=plugin&utm_campaign=renewal_deal&utm_source=renewal_deal_';
                                $link_renew = '<a href="'.$renew_url.'has_expired'.'" target="_blank" >'.__('Renew now.', WYSIJA).'</a>';
                                $error_msg=__('Your Premium licence has expired.',WYSIJA). ' ' . $link_renew;

                                break;
                            case 4: //You need to manually add this domain to your [link]account manager[/link]
                                $error_msg=__('You can add this domain to your [link]account manager[/link].',WYSIJA);
                                break;
                            case 5: //Your licence does not allow more domains, please upgrade your licence in your [link]account manager[/link]
                                $error_msg=__('Your licence doesn\'t allow more domains. Upgrade from your [link]account manager[/link].',WYSIJA);
                                break;
                            default:
                                $error_msg=$decoded['error'];
                        }
                        $this->error(str_replace(
                                array('[link]','[/link]','%1$s'),
                                array('<a href="http://www.mailpoet.com/account/licences/" target="_blank">','</a>',$decoded['domain']),
                                $error_msg), true);
                    }

                    // reset premium key data
                    $config_data = array('premium_key' => '', 'premium_val' => '');

                     WYSIJA::update_option('wysicheck', false);
                }

                // update config
                $model_config = WYSIJA::get('config','model');
                $model_config->save($config_data);
            }
        }else{
            $response['nocontact']=true;
            WYSIJA::update_option('wysicheck',true);
        }

        return $response;
    }


    function dkim_config(){


        //checkif the open ssl function for priv and ub key are present on that server
        $helper_toolbox = WYSIJA::get('toolbox','helper');
        $dkim_domain = $helper_toolbox->_make_domain_name(admin_url('admin.php'));
        $res1=$errorssl=false;
        if(function_exists('openssl_pkey_new')){
            while ($err = openssl_error_string());
            $res1=openssl_pkey_new(array('private_key_bits' => 1024));
            $errorssl=openssl_error_string();
        }

        if(function_exists('openssl_pkey_new') && $res1 && !$errorssl  && function_exists('openssl_pkey_get_details')){

            $rsaKey = array('private' => '', 'public' => '', 'error' => '');
            $res = openssl_pkey_new(array('private_key_bits' => 1024));

            if($res && !openssl_error_string()){
                // Get private key
                $privkey = '';
                openssl_pkey_export($res, $privkey);

                // Get public key
                $pubkey = openssl_pkey_get_details($res);


                $configData=array('dkim_domain'=>$dkim_domain,'dkim_privk'=>$privkey,'dkim_pubk'=>$pubkey['key'],'dkim_1024'=>1);

                $mConfig = WYSIJA::get('config','model');
                $mConfig->save($configData);
            }

        }else{//fetch them through a request to mailpoet.com
            $domainData=$this->getDomainInfo();
            $hHTTP = WYSIJA::get('http','helper');

            $jsonResult = $hHTTP->wp_request('http://www.mailpoet.com/?wysijap=checkout&wysijashop-page=1&controller=customer&action=checkDkimNew&data='.$domainData);

            //remotely connect to host
            if($jsonResult!==false){
                $decoded=json_decode($jsonResult);

                $configData=array('dkim_domain'=>$dkim_domain,'dkim_privk'=>$decoded->dkim_privk,'dkim_pubk'=>$decoded->dkim_pubk->key,'dkim_1024'=>1);

                $mConfig = WYSIJA::get('config','model');
                $mConfig->save($configData);
                WYSIJA::update_option('dkim_autosetup',false);

            }else{
                 WYSIJA::update_option('dkim_autosetup',true);
            }

        }

    }

}

