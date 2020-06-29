<?php

namespace MailOptin\FacebookCustomAudienceConnect;

class Subscription extends AbstractFacebookCustomAudienceConnect
{
    public $email;
    public $name;
    public $list_id;
    public $extras;
    /** @var Connect */
    public $connectInstance;

    public function __construct($email, $name, $list_id, $extras, $connectInstance)
    {
        $this->email           = $email;
        $this->name            = $name;
        $this->list_id         = $list_id;
        $this->extras          = $extras;
        $this->connectInstance = $connectInstance;

        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function subscribe()
    {
        try {

            $response = $this->fbca_instance()->addUserToCustomAudience(
                $this->list_id,
                $this->email,
                $this->get_first_name(),
                $this->get_last_name()
            );

            if (true === $response) {
                return parent::ajax_success();
            }

            $response = is_object($response) || is_array($response) ? json_encode($response) : $response;

            self::save_optin_error_log($response, 'facebookcustomaudience', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'facebookcustomaudience', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}