/**
 * External dependencies
 */
import { __, _x } from '@wordpress/i18n';
import { Button, Card, CardBody, CardHeader } from '@wordpress/components';
import { ONBOARDING_STORE_NAME } from '@woocommerce/data';
import {
	WooOnboardingTask,
} from '@woocommerce/onboarding';
import { registerPlugin } from '@wordpress/plugins';

/**
 * WooCommerce dependencies
 */
import { H } from '@woocommerce/components';
import { useDispatch } from "@wordpress/data";

const ConfigureSettingsTask = ( { onComplete } ) => {
	const { actionTask } = useDispatch( ONBOARDING_STORE_NAME );
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
						<Button variant="secondary" >
							{__( 'Configure fields now', 'woocommerce_gpf')}
						</Button>
					</a>&nbsp;&nbsp;
					<a target="_blank" rel="noopener noreferrer" href="https://woocommerce.com/document/google-product-feed-setting-product-data/">
						<Button variant="link">
							{__( 'Find out more', 'woocommerce_gpf')}
						</Button>
					</a>
				</p>
				<hr/>
				<p>
					<Button variant="primary" onClick={() => {
						actionTask( 'woocommerce-gpf-configure-settings' );
						onComplete();
					}}
					>
						{__( 'Action task', 'woocommerce_gpf' )}
					</Button>
				</p>
			</CardBody>
		</Card>
	);
};

const FeedSetupTask = ({ onComplete }) => {
	const { actionTask } = useDispatch( ONBOARDING_STORE_NAME );
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
						<Button variant="secondary" >
							{__( 'Follow the guide', 'woocommerce_gpf')}
						</Button>
					</a>
				</p>
				<hr/>
				<p>
					<Button variant="primary" onClick={() => {
						actionTask( 'woocommerce-gpf-feed-setup' );
						onComplete();
					}}
					>
						{__( 'Action task', 'woocommerce_gpf' )}
					</Button>
				</p>
			</CardBody>
		</Card>
	);
};

registerPlugin( 'woocommerce-gpf-configure-settings-content', {
	render: () => (
		<WooOnboardingTask id="woocommerce-gpf-configure-settings">
			{( {
				   onComplete,
				   query,
				   task,
			   } ) => <ConfigureSettingsTask onComplete={onComplete} task={task}/>}
		</WooOnboardingTask>
	),
	scope: 'woocommerce-tasks',
});

registerPlugin( 'woocommerce-gpf-feed-setup-content', {
	render: () => (
		<WooOnboardingTask id="woocommerce-gpf-feed-setup">
			{( {
				   onComplete,
				   query,
				   task,
			   } ) => <FeedSetupTask onComplete={onComplete} task={task} query={query}/>}
		</WooOnboardingTask>
	),
	scope: 'woocommerce-tasks',
});
