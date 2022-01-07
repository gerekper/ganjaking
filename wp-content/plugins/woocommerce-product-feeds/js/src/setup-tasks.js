/**
 * External dependencies
 */
import { __, _x } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import apiFetch from '@wordpress/api-fetch';
import { Button, Card, CardBody, CardHeader } from '@wordpress/components';

/**
 * WooCommerce dependencies
 */
import { H } from '@woocommerce/components';
import { getHistory, getNewPath } from '@woocommerce/navigation';

/* global woocommerce_gpf_setup_tasks_data */
const markTaskComplete = ( varName ) => {
	return () => {
		const dataObj = {};
		dataObj[ 'woocommerce_gpf_' + varName ] = true;
		apiFetch( {
					  path: '/wc-admin/options',
					  method: 'POST',
					  data: dataObj,
				  } )
			.then( () => {
				// Set the local concept of completeness to true so that task appears complete on the list.
				woocommerce_gpf_setup_tasks_data[ varName ] = true;
				// Redirect back to the root WooCommerce Admin page.
				getHistory().goBack();
			} );
	}
};

const markTaskIncomplete = ( varName ) => {
	return () => {
		const dataObj = {};
		dataObj[ 'woocommerce_gpf_' + varName ] = false;
		apiFetch( {
					  path: '/wc-admin/options',
					  method: 'POST',
					  data: dataObj,
				  } )
			.then( () => {
				// Set the local concept of completeness to true so that task appears complete on the list.
				woocommerce_gpf_setup_tasks_data[ varName ] = false;
				// Redirect back to the root WooCommerce Admin page.
				getHistory().goBack();
			} );
	}
};

const ConfigureSettingsTask = () => {
	return (
		<Card className="woocommerce-task-card">
			<CardHeader>
				<H>
					{__( 'WooCommerce Product Feeds: Configure feed settings', 'woocommerce_gpf' )}
				</H>
			</CardHeader>
			<CardBody>
				<p>
					{ __( 'WooCommerce Google Product Feed allows you to produce and submit a real-time feed of products and variations to Google Merchant Center.', 'woocommerce_gpf' ) } { __( 'Before you submit your feed to Google you should choose what information you want to send them, and how to map that data from your products. ', 'woocommerce_gpf')}
				</p>
				<p>
					{ __( "Once you've chosen which fields to send, you can set store-wide defaults, defaults against categories, or provide information on each individual product page. Alternatively you can choose to have the data pre-populated from existing taxonomies, or fields.", 'woocommerce_gpf' ) }
				</p>
				<p>
					<a target="_blank" rel="noopener noreferrer" href={ woocommerce_gpf_setup_tasks_data.settings_link}>
						<Button isSecondary >
							{__( 'Configure fields now', 'woocommerce_gpf')}
						</Button>
					</a>&nbsp;&nbsp;
					<a target="_blank" rel="noopener noreferrer" href="https://woocommerce.com/document/google-product-feed-setting-product-data/">
						<Button isLink>
							{__( 'Find out more', 'woocommerce_gpf')}
						</Button>
					</a>
				</p>
				<p>&nbsp;</p>
				<hr/>
				<div>
					{woocommerce_gpf_setup_tasks_data.configure_settings_is_complete ? (
						<Button isPrimary
								onClick={markTaskIncomplete( 'configure_settings_is_complete' )}>
							{__( 'Mark task incomplete', 'woocommerce_gpf' )}
						</Button>
					) : (
						 <Button isPrimary
								 onClick={markTaskComplete( 'configure_settings_is_complete' )}>
							 {__( 'Mark task complete', 'woocommerce_gpf' )}
						 </Button>
					 )}
				</div>
			</CardBody>
		</Card>
	);
};

const SetupFeedTask = () => {
	return (
		<Card className="woocommerce-task-card">
			<CardHeader>
				<H>
					{__( 'WooCommerce Product Feeds: Setup feed in Google Merchant Centre', 'woocommerce_gpf' )}
				</H>
			</CardHeader>
			<CardBody>
				<p>
					{ __( "Once you're happy with your feed setup, it's time to submit it to Google so they can import your product data.", 'woocommerce_gpf') }
				</p>
				<p>
					{ __( "Our online guide walks you through setting up your Google Merchant Centre account, adding shipping & tax settings, and having Google fetch your feed automatically every day to keep your product data up to date.", 'woocommerce_gpf') }
				</p>
				<p>
					<a target="_blank" rel="noopener noreferrer" href="https://woocommerce.com/document/google-product-feed-setting-up-your-feed-google-merchant-centre/">
						<Button isSecondary >
							{__( 'Follow the guide', 'woocommerce_gpf')}
						</Button>
					</a>
				</p>
				<p>&nbsp;</p>
				<hr/>
				<div>
					{woocommerce_gpf_setup_tasks_data.feed_setup_is_complete ? (
						<Button isPrimary onClick={markTaskIncomplete( 'feed_setup_is_complete' )}>
							{__( 'Mark task incomplete', 'woocommerce_gpf' )}
						</Button>
					) : (
						 <Button isPrimary onClick={markTaskComplete( 'feed_setup_is_complete' )}>
							 {__( 'Mark task complete', 'woocommerce_gpf' )}
						 </Button>
					 )}
				</div>
			</CardBody>
		</Card>
	);
};

/**
 * Use the 'woocommerce_admin_onboarding_task_list' filter to add a task page.
 */
addFilter(
	'woocommerce_admin_onboarding_task_list',
	'woocommerce_gpf',
	( tasks ) => {
		return [
			...tasks,
			{
				key: 'woocommerce_gpf_configure_settings',
				title: __( 'WooCommerce Product Feeds: Configure feed settings', 'woocommerce_gpf' ),
				container: <ConfigureSettingsTask/>,
				completed: woocommerce_gpf_setup_tasks_data.configure_settings_is_complete,
				visible: true,
				additionalInfo: __( 'Choose what information you want to send to Google, and how to map it from your products.', 'woocommerce_gpf' ),
				time: _x( '5 minutes', 'Estimated time to complete setup task', 'woocommerce_gpf' ),
				isDismissable: false,
			},
			{
				key: 'woocommerce_gpf_feed_setup',
				title: __( 'WooCommerce Product Feeds: Set up feed in Google Merchant Centre', 'woocommerce_gpf' ),
				container: <SetupFeedTask/>,
				completed: woocommerce_gpf_setup_tasks_data.feed_setup_is_complete,
				visible: true,
				additionalInfo: __( "Submit your feed to Google so they import your product data.", 'woocommerce_gpf'),
				time: _x( '5 minutes', 'Estimated time to complete setup task', 'woocommerce_gpf' ),
				isDismissable: false,
			},
		];
	}
);
