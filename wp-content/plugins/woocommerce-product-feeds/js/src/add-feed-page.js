import { addFilter } from '@wordpress/hooks';

const AddFeedPage = () => <h1>My Example Extension</h1>;

addFilter( 'woocommerce_admin_pages_list', 'my-namespace', ( pages ) => {
	console.log('HERE');
	pages.push( {
					container: AddFeedPage,
					path: 'woocommerce_product_feeds/feeds/add',
					breadcrumbs: [ 'WooCommerce Product Feeds', 'Add Feed' ],
					navArgs: {
						id: 'woocommerce-product-feeds-feed-add',
					},
				} );

	return pages;
} );
