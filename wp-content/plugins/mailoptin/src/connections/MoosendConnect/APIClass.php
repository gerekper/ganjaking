<?php

namespace MailOptin\MoosendConnect;

class APIClass
{
    protected $api_key;

    protected $api_url = "https://api.moosend.com/v3/";


    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    /**
     * @param $endpoint
     * @param array $args
     * @param string $method
     * @return array
     * @throws \Exception
     */
    public function make_request($endpoint, $args = [], $method = 'get')
    {
        //Request for JSON responses
        $endpoint = $endpoint . '.json';

        $url      = add_query_arg('apikey', $this->api_key, $this->api_url . $endpoint);

        //Prepare http args
        $wp_args = [
            'method'        => strtoupper($method),
            'timeout'       => 30,
            "headers"       => [ "Accept" => "application/json" ],
        ];

        switch ($method) {
            case 'post':
                $wp_args['headers'][ "Content-Type" ] = "application/json";
                $wp_args['body']    = json_encode( $args );
                break;
            case 'get':
                $url = add_query_arg($args, $url);
                break;
        }

        
        $response = wp_remote_request($url, $wp_args);

        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        $response = json_decode( wp_remote_retrieve_body( $response ) );
        if (! empty( $response->Error ) ) {
            throw new \Exception( $response->Error );
        }

        return $response;
    }

    /**
     * @param $endpoint
     * @param array $args
     *
     * @return array
     * @throws \Exception
     */
    public function post( $endpoint, $args = [] )
    {
        return $this->make_request( $endpoint, $args, 'post' );
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function get_lists()
    {
        $response = $this->make_request('lists/1/1000');

        /**
         * 
         * A list with mailing lists containing the following information for each list:
         * ID : The id of the mailing list.
         * Name : The name of the mailing list.
         * ActiveMemberCount : The number of the active members for this mailing list.
         * BouncedMemberCount : The number of the bounced emails for this mailing list.
         * RemovedMemberCount : The number of the removed emails for this mailing list.
         * UnsubscribedMemberCount : The number of the unsubscribed emails for this mailing list.
         * Status : The status of the mailing list. For created this will be 0. For imported it will be 1. For importing it will be 2 is for and 3 for deleted.
         * CustomFieldsDefinition : The details of the custom fields for the requested mailing list.
         *          ID : The id of the custom field.
         *          Name : The name of the custom field.
         *          Context : The context of the custom field. This will be null if not singleSelectDropDown.
         *          IsRequired : False if the custom field is not required, true if it is.
         *          Type : 0 for text, 1 for number, 2 for dateTime, 3 for singleSelectDropDown, 5 for checkbox.
         */
        return $response->Context->MailingLists;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function get_custom_fields( $list_id )
    {
        $response = $this->make_request( "lists/$list_id/details" );
        $fields   = $response->Context->CustomFieldsDefinition;

        /**
         * This will be blank if there are no custom fields for this mailing list.
         * 
         * ID : The id of the custom field
         * Name : The name of the custom field
         * Context : The context of the custom field. Will be null if not singleSelectDropDown.
         * IsRequired : False if the custom field is not required, true if it is
         * Type : 0 for text, 1 for number, 2 for dateTime, 3 for singleSelectDropDown, 5 for checkbox.
         */
        return $fields;
    }

    /**
     * @param $list_id
     * @param $args array in the form:
     *      Name : The name of the member. (Optional)
     *      Email : The email address of the member. (Required)
     *      HasExternalDoubleOptIn: When true, flags the added member as having given their subscription consent by other means. (Optional)
     *      CustomFields : A list of name=value pairs that match the member's custom fields defined in the mailing list. (Optional) 
     * 
     * @return array
     * @throws \Exception
     */
    public function add_subscriber($list_id, $args )
    {
        return $this->post( "subscribers/$list_id/subscribe", $args );
    }

    /**
     * @param $args array of:
     * 
     * Name : The name of the campaign. (Required)
     * Subject : The subject line of the new campaign. (Required)
     * SenderEmail : The sender email of the campaign. (Required)
     * ReplyToEmail : The email address to which recipients replies will arrive. It must be one of your sender accounts. If not specified, the sender's email will be assumed. (Required)
     * ConfirmationToEmail : The email address to which a confirmation message will be sent when the campaign has been successfully sent. This can be any valid email address. It does not have to be one of your sender signatures. If not specified, the sender's email will be assumed. (Optional)
     * WebLocation : A url to retrieve the html content for the campaign. We'll automatically move all CSS inline. (Optional)
     * 
     * @return array
     * @throws \Exception
     */
    public function create_campaign( $list_id, $args )
    {
        $args['IsAB']         = 'false';
        $args['MailingLists'] = [[ 'MailingListID' => $list_id]];
        $res = $this->post( "campaigns/create", $args );
        return $res->Context; //Id of the created campaign

    }

    /**
     * @param $campaign_id
     * 
     * @return array
     * @throws \Exception
     */
    public function send_campaign( $campaign_id )
    {
        return $this->post( "campaigns/$campaign_id/send" );
    }
}