/**
 * External dependencies
 */
 import { __, sprintf } from '@wordpress/i18n';
 import { __experimentalRegisterCheckoutFilters } from '@woocommerce/blocks-checkout';

__experimentalRegisterCheckoutFilters(
    'mix-and-match',
    {

	 cartItemClass: ( classlist, { mix_and_match }, { context, cartItem } ) => {

		 if ( mix_and_match ) {

			 let classes = [];

			 if ( mix_and_match.container ) {

				 classes.push( 'is-mnm-child' );

				 classes.push( 'is-mnm-child__cid_' + mix_and_match.child_item_data.container_id );
				 classes.push( 'is-mnm-child__iid_' + mix_and_match.child_item_data.child_item_id );

				 if ( mix_and_match.child_item_data.is_last ) {
					 classes.push( 'is-mnm-child__last' );
				 }

				 if ( mix_and_match.child_item_data.is_priced_per_product ) {
					 classes.push( 'is-mnm-child__priced_per_product' );
				 }

			 } else if ( mix_and_match.child_items ) {

				 classes.push( 'is-mnm-container' );

				 classes.push( 'is-mnm-container__cid_' + cartItem.id );

				 if ( mix_and_match.container_data.is_editable ) {
					 classes.push( 'is-mnm-container__editable' );
				 }

				 if ( mix_and_match.container_data.is_priced_per_product ) {
					 classes.push( 'is-mnm-container__priced_per_product' );
				 }

			 }

			 if ( classes.length ) {
				 classlist += ' ' + classes.join( ' ' );
			 }
		 }

		 return classlist;
	 }
    } 
);
