<?php

/**
 * Sort items based on their calculated score. 
 * 
 * Sorting helper is initizlized with a keyd array of Post ID's and the calculated score.  
 * The sort method is called with a list of Posts and sorted based on the scores used to create the object. 
 */
class WC_Recommender_Sorting_Helper {

    private $scores;

    public function __construct($scores) {
        $this->scores = $scores;
    }
    
    /**
     * Sorts an array of WP_Posts based on the scores used to create the WC_Recommender_Sorting_Helper object. 
     * @param WP_Post $a
     * @param WP_Post $b
     * @return int
     */
    public function sort($a, $b) {
        if ($this->scores[$a->ID] < $this->scores[$b->ID]) {
            return -1;
        } elseif ($this->scores[$a->ID] > $this->scores[$b->ID]) {
            return 1;
        } else {
            return 0;
        }
    }

	/**
	 * Sorts an array of WP_Posts based on the scores used to create the WC_Recommender_Sorting_Helper object.
	 * @param WP_Post $a
	 * @param WP_Post $b
	 * @return int
	 */
	public function sort_also_viewed($a, $b) {
		if ($this->scores[$a->ID] > $this->scores[$b->ID]) {
			return -1;
		} elseif ($this->scores[$a->ID] < $this->scores[$b->ID]) {
			return 1;
		} else {
			return 0;
		}
	}

}