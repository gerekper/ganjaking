<?php
/** 
 * Extension to eventon seats class just for frontend functions
 * @version 1.2
 */

class EVOST_Seats_Seat extends EVOST_Expirations{

	public $seat_slug= false;
	public $seats_data = false;

	public function __construct($EVENT, $wcid, $seat_slug){
		parent::__construct($EVENT, $wcid);
		$this->seat_slug = $seat_slug;

		$this->_localize_seat_slug($this->seat_slug);
	}

	// getters
		function get_price(){
			if($this->seat_type=='seat') return $this->get_item_prop('price');
			return $this->get_item_prop('def_price');
		}
		function get_seat_number(){
			return ($this->seat_type=='seat') ? $this->get_item_prop('number') : $this->get_item_prop('section_name');
		}
		function get_max_capacity(){
			if($this->seat_type=='seat') return 1;
			return $this->nonseat_get_available_seats( $this->seat_type );
		}
		function get_readable_seat_number(){
			$O = array();
			$O['section'] = $this->get_section_letter_by_id();
			$O['row'] = $this->get_row_letter_by_id();
			$O['seat'] = $this->get_seat_number();
			return $O;
		}

		// there is get_seat_status(seatslug) on par obj
		function get_this_seat_status(){
			if($this->seat_type == 'seat'){
				$status = $this->get_item_prop('status');
				if(!$status) return false;
				return $status;
			}else{ // for unassigned seats				
				return true;
			}
		}
		
	// Validate
		// returns if a seat is in cart
		function is_seat_in_cart(){
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				// skips
				if(empty($values['evost_data'])) continue;
				if(empty($values['evost_data']['seat_slug']) ) continue;

				if($values['evost_data']['seat_slug'] == $this->seat_slug ) return true;
			}
			return false;
		}

		// check if seat is available 
		function is_seat_available($qty=1){
			$seat = $this->item_data;

			if( $this->seat_type == 'seat'){
				$status = $this->get_item_prop('status');
				if(!$status) return false;

				// if seat is tuav
				if( $status == 'tuav'  ){
					return $this->is_seat_in_cart() ? true: false;
				}
				if( $status == 'av'  ) return true;
			}else{
				// get available non seats quantity		
				$available = $this->nonseat_get_available_seats( $this->seat_type );		

				// for booth seats
				if( $this->seat_type == 'booseat'){
					return ( $available && $available > 0 ) ? true: false;
				}
	
				if($available  && $available >= $qty) return true;
			}
			return false;
		}

		function is_seat_slug_exists(){
			$a = array(
				'post_type'=> 'evo-tix',
				'posts_per_page'=>-1,
				'meta_key'=>'_evost_seat_slug',
				'meta_value'=> $this->seat_slug
			);
			$res = new WP_Query( $a);
			return $res->have_posts()? true: false;
		}

}