<?php

namespace MailOptin\MoosendConnect;

class Subscription extends AbstractMoosendConnect
{
    public $email;
    public $name;
    public $list_id;
    public $extras;

    public function __construct($email, $name, $list_id, $extras)
    {
        $this->email   = $email;
        $this->name    = $name;
        $this->list_id = $list_id;
        $this->extras  = $extras;

        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function subscribe()
    {
        try {

            $args = [
                'Name'                   => $this->name,
                'Email'                  => $this->email,
                'HasExternalDoubleOptIn' => false,
                'CustomFields'           => [],
            ];

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {

                foreach ($custom_field_mappings as $MoosendKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesn't have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        // date field just works. no multi-select support
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        $args['CustomFields'][] = "$MoosendKey=$value";
                    }
                }
            }

            $args = array_filter($args, [$this, 'data_filter']);

            $this->moosend_instance()->add_subscriber($this->list_id, $args);

            return parent::ajax_success();

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'moosend', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}