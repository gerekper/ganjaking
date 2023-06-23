/**
 * External dependencies
 * NB: registerCheckoutFilters was "graduated" in Woo 7.5.0 or Woo Blocks 9.6.0
 */
import {
	__experimentalRegisterCheckoutFilters as experimentalFilters,
	registerCheckoutFilters as graduatedFilters,
} from '@woocommerce/blocks-checkout';

const registerCheckoutFilters =
	typeof graduatedFilters !== 'undefined'
		? graduatedFilters
		: experimentalFilters;

registerCheckoutFilters( 'mix-and-match', {

	itemName: ( context, { mix_and_match }, { cartItem } ) => {

		if ( mix_and_match && mix_and_match.child_item_data ) {
			context = `${ context } x ${ mix_and_match.child_item_data.child_qty }`;
		}

		return context;
	},

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
