<?php
namespace WPManageNinja\Lead;

class ReviewOptIn
{
	private $options;
	private $dismissTime = 604800; // 7 Days

	public function __construct($optionArray) {
		$this->options = $optionArray;
	}

	/**
	 * Check If User already consent. If consented then don't show
	 * Or If user dismissed then check if $this->dismissTime is over.
	 * If within the time then don't show
	 * Otherwise we can show this message
	 * @return bool
	 */
	public function noticeable()
	{
		$optStatus = $this->status();
		if( $optStatus == 'yes') {
			return false;
		} else if($optStatus == 'no') {
		    $noDismissTime = $this->getValue('review_no_optin_dismiss_time');
            if( $noDismissTime && ( time() - intval($noDismissTime) < 2419200 ) ) { // 28 days
                return false;
            }
        }
		// check if user dismissed
		$dismissTime = $this->getValue('review_optin_dismiss_time');
		if( $dismissTime && ( time() - intval($dismissTime) < $this->dismissTime ) ) {
			return false;
		}
		return true;
	}

	public function getNotice()
    {
        return '';
    }

    public function addAssets() {

    }

    public function doConsent($status)
    {
        if($status == 'yes') {
            $this->options['review_optin_status'] = 'yes';
        } else if($status == 'no') {
            $this->options['review_optin_status'] = 'no';
            $this->options['review_no_optin_dismiss_time'] = time();
        } else if($status == 'dismiss') {
            $this->options['review_optin_status'] = 'dismiss';
            $this->options['review_optin_dismiss_time'] = time();
        }
        update_option('_ninja_table_lead_options', $this->options);
    }

	public function status()
	{
		return $this->getValue('review_optin_status');
	}

	private function getValue($key) {
		if (isset($this->options[$key])) {
			return $this->options[$key];
		}
		return false;
	}
}
