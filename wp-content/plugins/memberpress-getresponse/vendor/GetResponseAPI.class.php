<?php

/**
 * GetResponsePHP is a PHP5 implementation of the GetResponse API
 * @internal This wrapper is incomplete and subject to change.
 * @authors Ben Tadiar <ben@bentadiar.co.uk>, Robert Staddon <robert@abundantdesigns.com>
 * @copyright Copyright (c) 2010 Assembly Studios
 * @link http://www.assemblystudios.co.uk
 * @package GetResponsePHP
 * @version 0.1.1
 */

/**
 * GetResponse Class
 * @package GetResponsePHP
 */
if(!class_exists('MeprGetResponse')) { //Fixes issues with other plugins also using this class
  class MeprGetResponse {
    /**
     * GetResponse API key
     * http://www.getresponse.com/my_api_key.html
     * @var string
     */
    public $apiKey = 'PASS_API_KEY_WHEN_INSTANTIATING_CLASS';
    
    /**
     * GetResponse API URL
     * @var string
     * @access private
     */
    private $apiURL = 'https://api.getresponse.com/v3';
    
    /**
     * Check cURL extension is loaded and that an API key has been passed
     * @param string $apiKey GetResponse API key
     * @return void
     */
    public function __construct($apiKey = null)
    {
      if(!extension_loaded('curl')) trigger_error('GetResponsePHP requires PHP cURL', E_USER_ERROR);
      if(is_null($apiKey)) trigger_error('API key must be supplied', E_USER_ERROR);
      $this->apiKey = $apiKey;
    }
    
    /**
     * Test connection to the API, returns "pong" on success
     * @return string
     */
    public function ping()
    {
      $request  = $this->prepRequest();
      $response = $this->execute($request, 'accounts');

        return $response;
    }
    
    /**
     * Get a list of active campaigns, optionally filtered
     * @param string $name filtered name
     * @return object 
     */
    public function getCampaigns($name = null)
    {
      $params = null;
      if(!empty($name)) $params = array('query[name]' => array($name));
      $request  = $this->prepRequest($params);
      $response = $this->execute($request, 'campaigns');

      return $response;
    }

    /**
     * Return a list of contacts by email address (optionally narrowed by campaign)
     * @param string $email Email Address of Contact (or a string contained in the email address)
     * @return object 
     */
    public function getContactsByEmail($email)
    {
	  $params = null;
	  if(!empty($email)) $params = 'query[email]='.$email;
	  $response = $this->execute($params, 'contacts');
      return $response;
    }
    
    /**
     * Add contact to the specified list (Requires email verification by contact)
     * The return value of this function will be "queued", and on subsequent
     * submission of the same email address will be "duplicated".
     * @param string $campaign Campaign ID
     * @param string $name Name of contact
     * @param string $email Email address of contact
     * @param int $cycle_day
     * @param array $customs
     * @return object
     */
	  public function addContact( $campaign, $name, $email, $cycle_day = 0, $customs = array() ) {
		  $obj             = new stdClass();
		  $obj->campaignId = $campaign;
		  $params = array(
			  'campaign'   => $obj,
			  'email'      => $email,
			  'dayOfCycle' => $cycle_day,
			  'ipAddress'  => $_SERVER['REMOTE_ADDR']
		  );
		  if ( ! empty( trim($name) ) ) {
			  $params['name'] = $name;
		  }
		  if ( ! empty( $customs ) ) {
			  foreach ( $customs as $key => $val ) {
				  $c[] = array( 'customFieldId' => $key, 'value' => $val );
			  }
			  $params['customFieldValues'] = $c;
		  }
		  $request  = $this->prepRequest( $params );
		  $response = $this->execute( $request, 'contacts', 'POST' );

		  return $response;
	  }
    
    /**
     * Delete a contact
     * @param string $id
     * @return object
     */
    public function deleteContact($id)
    {
      $params = array( 'id' => $id );
      $request  = $this->prepRequest($params);
      $response = $this->execute($request, 'contacts/'.$id, 'DELETE');
      return $response;
    }
    
    /**
     * Return array as a JSON encoded string
     * @param array  $params Array of parameters
     * @return string JSON encoded string
     * @access private
     */
    private function prepRequest($params = null, $id = null)
    {
      $array = array();
      if(!is_null($params)) $array[0] = $params;
      $request = json_encode( $params );
      return $request;
    }
    
    /**
     * Executes an API call
     * @param string $request JSON encoded array
     * @param string $url
     * @param string $method
     * @return mixed
     * @access private
     */
    private function execute($request, $url = 'accounts', $method = 'GET')
    {
	    $handle = curl_init();
	    if ( 'POST' === $method ) {
		    curl_setopt($handle, CURLOPT_POST, 1);
		    curl_setopt($handle, CURLOPT_POSTFIELDS, $request);
	    }
	    if ( 'DELETE' === $method ) {
		    curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
	    }
	    $header = array();
	    $header[] = "X-Auth-Token: api-key $this->apiKey";
	    $header[] = 'Content-Type: application/json';

	    if ( 'POST' === $method || 'DELETE' === $method ) {
		    curl_setopt($handle, CURLOPT_URL, "$this->apiURL/$url");
	    } else {
		    curl_setopt($handle, CURLOPT_URL, "$this->apiURL/$url?$request");
	    }
        curl_setopt($handle, CURLOPT_HTTPHEADER, $header);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	    $response = json_decode(curl_exec($handle));
	    if(curl_error($handle)) trigger_error(curl_error($handle), E_USER_ERROR);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if(!(($httpCode == '200') || ($httpCode == '204') || ($httpCode == '202') || ($httpCode == '409') || ($httpCode == '401') || ($httpCode == '400'))) {
	      trigger_error('API call failed. Server returned status code '.$httpCode, E_USER_ERROR);
        }
        curl_close($handle);
	    if((($httpCode == '200') || ($httpCode == '204') || ($httpCode == '202') || ($httpCode == '409'))) {
	      if ( $url !== 'accounts' ) {
		      return $response;
	      }
	      return $response->accountId;
        } elseif ($httpCode == '401' && $url === 'accounts') {
      	    return 'error';
        }
    }
  } //End class
} //End if class_exists
