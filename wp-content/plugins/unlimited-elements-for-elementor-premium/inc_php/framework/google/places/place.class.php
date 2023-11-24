<?php

class UEGoogleAPIPlace extends UEGoogleAPIModel{

	/**
	 * Get the reviews.
	 *
	 * @return UEGoogleAPIPlaceReview[]
	 */
	public function getReviews(){

		$reviews = $this->getAttribute("reviews", array());
		$reviews = UEGoogleAPIPlaceReview::transformAll($reviews);

		return $reviews;
	}

}
