<?php

namespace WPManageNinja\Lead;

class LeadFlow {
	private $notices = [];
	private $assetStatus = false;
	private $options = array();
	public function boot() {
		if ( is_multisite() || ! current_user_can( ninja_table_admin_role() ) ) {
			return;
		}

		$this->options = get_option( '_ninja_table_lead_options', array() );
		$this->loadDependencies();

		// Lead Filters
		add_filter('ninja_tables_show_lead', array( $this, 'leadStatus') );
		add_action( 'wp_ajax_ninja_table_lead_optin', array( $this, 'leadOptinAction' ) );
		add_action('ninja_table_lead_optin_yes', array($this, 'optinLeadYes'));

		// Review Filters
        add_filter('ninja_tables_show_review_optin', array( $this, 'reviewOptionStatus') );
        add_action( 'wp_ajax_ninja_table_review_consent', array( $this, 'reviewOptinAction' ) );

    }

	public function optinLeadYes($options)
    {
	    $lead = new LeadOptIn( $this->options );
	    $lead->subscribe();
    }

	public function leadStatus( $status ) {
		$lead = new LeadOptIn( $this->options );
		if ( $lead->noticeable() ) {
			return true;
		}
		return $status;
	}

	public function reviewOptionStatus($status) {
        $reviewOption = new ReviewOptIn( $this->options );
        if ( $reviewOption->noticeable() ) {
            return true;
        }
        return $status;
    }

    public function reviewOptinAction() {
	    $status = sanitize_text_field($_REQUEST['status']);
        $reviewOption = new ReviewOptIn( $this->options );
        $reviewOption->doConsent($status);
        wp_send_json_success(array(
                'message' => 'Thank you'
        ), 200);
    }


	public function addLeadNotice() {
		$lead = new LeadOptIn( $this->options );
		if ( $lead->noticeable() ) {
			$this->addNotice(
				$lead->getNotice()
			);
			add_action( 'admin_notices', array( $this, 'showNotices' ) );
			$this->assetStatus = true;
		} else {
		    $reviewOptIn = new ReviewOptIn($this->options);
		    if($reviewOptIn->noticeable()) {
                $this->addNotice(
                    $reviewOptIn->getNotice()
                );
                add_action( 'admin_notices', array( $this, 'showNotices' ) );
                $reviewOptIn->addAssets();
            }
        }
	}

	public function loadDependencies() {
		include 'LeadOptIn.php';
		include 'ReviewOptIn.php';
	}

	public function addNotice( $message, $type = 'success', $id = '', $hasDismiss = true ) {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'ninja_tables' ) {
			return;
		}
		$this->notices[ $id ] = array(
			'message' => $message,
			'type'    => $type,
			'id'      => $id,
			'dismiss' => $hasDismiss,
			'title'   => 'Ninja Tables'
		);
	}

	public function showNotices() {
		foreach ( $this->notices as $notice ) {
			?>
            <div data-notice_id="<?php echo $notice['id']; ?>"
                 class="notice notice-<?php echo $notice['type']; ?> mn-notices mn-has-title">
				<?php if ( $notice['title'] ): ?>
                    <label class="mn-plugin-title"><?php echo $notice['title']; ?></label>
				<?php endif; ?>
                <div class="mn-close">
                    <i class="dashicons dashicons-no" title="Dismiss"></i><span>Dismiss</span>
                </div>
                <div class="mn-notice-body">
					<?php echo $notice['message']; ?>
                </div>
            </div>
			<?php
		}
	}

	private function insertCss() {
		?>
        <style type="text/css">
            .mn-notices {
                position: relative
            }

            .mn-notices.mn-has-title {
                margin-bottom: 30px !important
            }

            .mn-notices.success {
                color: green
            }

            .mn-notices .mn-notice-body {
                margin: .5em 0;
                padding: 2px
            }

            .mn-notices .mn-close {
                cursor: pointer;
                color: #aaa;
                float: right
            }

            .mn-notices label.mn-plugin-title {
                background: rgba(0, 0, 0, 0.3);
                color: #fff;
                padding: 2px 10px;
                position: absolute;
                top: 100%;
                bottom: auto;
                right: auto;
                -moz-border-radius: 0 0 3px 3px;
                -webkit-border-radius: 0 0 3px 3px;
                border-radius: 0 0 3px 3px;
                left: 10px;
                font-size: 12px;
                font-weight: bold;
                cursor: auto;
            }

            .mn-notices .mn-close:hover {
                color: #666
            }

            .mn-notices .mn-close > * {
                margin-top: 7px;
                display: inline-block
            }

            .mn-notice label.mn-plugin-title {
                background: rgba(0, 0, 0, 0.3);
                color: #fff;
                padding: 2px 10px;
                position: absolute;
                top: 100%;
                bottom: auto;
                right: auto;
                -moz-border-radius: 0 0 3px 3px;
                -webkit-border-radius: 0 0 3px 3px;
                border-radius: 0 0 3px 3px;
                left: 10px;
                font-size: 12px;
                font-weight: bold;
                cursor: auto
            }

            div.mn-notice.updated, div.mn-notice.success, div.mn-notice.promotion {
                display: block !important
            }
        </style>
		<?php
	}

	public function leadOptinAction() {
		$status  = sanitize_text_field( $_REQUEST['status'] );
		$this->options['lead_optin_status'] = $status;
		$this->options['lead_optin_time'] = time();
		update_option( '_ninja_table_lead_options', $this->options );
		do_action( 'ninja_table_lead_optin_' . $status, $this->options );

		if($status == 'yes') {
		    $message = 'Thank you for subscribe to our product update notifications.';
        } else {
			$message = 'You have declined to opt-in. Thank You';
        }
		wp_send_json_success( array(
			'message' => $message
		), 200 );
	}
}
