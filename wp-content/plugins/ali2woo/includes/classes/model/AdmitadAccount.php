<?php

/**
 * Description of AdmitadAccount
 *
 * @author Ali2Woo Team
 */

namespace Ali2Woo;

class AdmitadAccount extends AbstractAccount  {
    static public function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getDeeplink($hrefs) {
        $result = array();
        if ($hrefs) {
            $admitad_account = Account::getInstance()->get_admitad_account();
            if (!empty($admitad_account['cashback_url'])) {
                $hrefs = is_array($hrefs) ? array_values($hrefs) : array(strval($hrefs));
                foreach($hrefs as $href){
                    $href2 = $this->getNormalizedLink($href);

                    if(parse_url($admitad_account['cashback_url'], PHP_URL_QUERY)){
                        $cashback_url = $admitad_account['cashback_url'].'&ulp='.urlencode($href2);
                    }else {
                        $cashback_url = $admitad_account['cashback_url'].'?ulp='.urlencode($href2);
                    }
                    
                    $result[] = array('url'=>$href, 'promotionUrl'=>$cashback_url);
                }
            }
        }
        return $result;
    }
}
