<?php

/**
 * Description of AliexpressAccount
 *
 * @author Ali2Woo Team
 */

namespace Ali2Woo;

class AliexpressAccount extends AbstractAccount  {
    static public function getInstance(): ?AliexpressAccount
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getDeeplink($hrefs): array
    {
        $result = [];
        if ($hrefs) {
            $account = Account::getInstance()->get_aliexpress_account();

            //set default trackingid if it's not set
            $account['trackingid'] = $account['trackingid'] ?: 'default';

            $isAffiliateAccountSet = !empty($account['appkey']) &&
                !empty($account['secretkey']) &&
                !empty($account['trackingid']);

            if ($isAffiliateAccountSet) {
                $hrefs = is_array($hrefs) ? array_values($hrefs) : [strval($hrefs)];

                $nHrefs = [];
                foreach($hrefs as $href){
                    $nHrefs[$this->getNormalizedLink($href)] = $href;
                }

                $client = new \IopClient(
                    'https://api-sg.aliexpress.com/sync',
                    $account['appkey'],
                    $account['secretkey']
                );
                $client->readTimeout = '6';

                $sourceValues = implode(',', array_keys($nHrefs));

                $request = new \IopRequest('aliexpress.affiliate.link.generate');
                $request->addApiParam('promotion_link_type','2');
                $request->addApiParam('source_values',$sourceValues);
                $request->addApiParam('tracking_id', $account['trackingid']);

                try {
                    $response = $client->execute($request);
                    $response = json_decode($response);

                    if (!isset($response->aliexpress_affiliate_link_generate_response)) {
                        if (isset($response->error_response)) {
                            a2w_error_log('AliexpressAccount::getDeeplink error: ' .
                                ($response->error_response->msg ?? 'Unknown error')
                            );
                        }

                        return [];
                    }

                    $responseResult = $response->aliexpress_affiliate_link_generate_response->resp_result;

                    if ($responseResult->resp_code == 200){
                        foreach($responseResult->result->promotion_links->promotion_link as $pl){
                            if(isset($pl->promotion_link) && isset($nHrefs[$pl->source_value])){
                                $result[] = [
                                    'url'=>$nHrefs[$pl->source_value],
                                    'promotionUrl'=>$pl->promotion_link
                                ];
                            }
                        }
                    } else {
                        $isAffiliateProduct = $responseResult->resp_code !== 405;
                        if ($isAffiliateProduct) {
                            a2w_error_log('AliexpressAccount::getDeeplink error: '.
                                (!empty($responseResult->resp_msg) ? $responseResult->resp_msg : 'Unknown error'));
                        }
                    }
                }
                catch (\Exception) {
                    //such exception is logged by lib itself
                }
            }
        }

        return $result;
    }
}
