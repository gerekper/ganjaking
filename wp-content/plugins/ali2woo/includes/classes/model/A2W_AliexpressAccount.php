<?php

/**
 * Description of A2W_AliexpressAccount
 *
 * @author Andrey
 */
if (!class_exists('A2W_AliexpressAccount')) {

    class A2W_AliexpressAccount extends A2W_AbstractAccount  {
        static public function getInstance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function getDeeplink($hrefs) {
            $result = array();
            if ($hrefs) {
                $account = A2W_Account::getInstance()->get_aliexpress_account();
                if (!empty($account['appkey']) && !empty($account['secretkey']) && !empty($account['trackingid'])) {
                    $hrefs = is_array($hrefs) ? array_values($hrefs) : array(strval($hrefs));

                    $nHrefs = array();
                    foreach($hrefs as $href){
                        $nHrefs[$this->getNormalizedLink($href)] = $href;
                    }

                    $client = new TopClient;  
                    $client->appkey = $account['appkey'];
                    $client->secretKey = $account['secretkey'];
                    $client->format = 'json';
                    $req = new AliexpressAffiliateLinkGenerateRequest;
                    $req->setPromotionLinkType("2");
                    $req->setSourceValues(implode(',', array_keys($nHrefs)));
                    $req->setTrackingId($account['trackingid']);
                    $resp = $client->execute($req);

                    if($resp->resp_result->resp_code == 200){
                        foreach($resp->resp_result->result->promotion_links->promotion_link as $pl){
                            if(isset($pl->promotion_link) && isset($nHrefs[$pl->source_value])){
                                $result[] = array('url'=>$nHrefs[$pl->source_value], 'promotionUrl'=>$pl->promotion_link);
                            }
                        }
                    } else{
                        a2w_error_log('A2W_AliexpressAccount::getDeeplink error: '.(!empty($resp->resp_result->resp_msg)?$resp->resp_result->resp_msg:'Unknown error'));
                    }
                }
            }
            return $result;
        }
    }

}
