<?php

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


	class UniteFunctionsWPUC{

		public static $urlSite;
		public static $urlAdmin;
		private static $db;
		private static $objAcfIntegrate;
		private static $cacheTermCustomFields = array();
		private static $cacheUserCustomFields = array();
		private static $cacheTermParents = array();

		private static $arrTermParentsCache = array();
		private static $arrTaxCache;
		private static $arrUrlThumbCache = array();
		private static $arrUrlAttachmentDataCache = array();
		private static $cacheAuthorsShort = null;
		private static $arrThumbSizesCache = null;
		public static $arrLastTermsArgs;
		public static $cachePostContent = array();

		const SORTBY_NONE = "none";
		const SORTBY_ID = "ID";
		const SORTBY_AUTHOR = "author";
		const SORTBY_TITLE = "title";

		const SORTBY_PRICE = "price";
		const SORTBY_SALE_PRICE = "sale_price";
		const SORTBY_SALES = "sales";
		const SORTBY_RATING = "rating";

		const SORTBY_SLUG = "name";
		const SORTBY_DATE = "date";
		const SORTBY_LAST_MODIFIED = "modified";
		const SORTBY_RAND = "rand";
		const SORTBY_COMMENT_COUNT = "comment_count";
		const SORTBY_MENU_ORDER = "menu_order";
		const SORTBY_PARENT = "parent";
		const SORTBY_META_VALUE = "meta_value";
		const SORTBY_META_VALUE_NUM = "meta_value_num";

		const ORDER_DIRECTION_ASC = "ASC";
		const ORDER_DIRECTION_DESC = "DESC";

		const THUMB_SMALL = "thumbnail";
		const THUMB_MEDIUM = "medium";
		const THUMB_LARGE = "large";
		const THUMB_MEDIUM_LARGE = "medium_large";
		const THUMB_FULL = "full";

		const STATE_PUBLISHED = "publish";
		const STATE_DRAFT = "draft";


		/**
		 *
		 * init the static variables
		 */
		public static function initStaticVars(){

			self::$urlSite = site_url();

			if(substr(self::$urlSite, -1) != "/")
				self::$urlSite .= "/";

			self::$urlAdmin = admin_url();
			if(substr(self::$urlAdmin, -1) != "/")
				self::$urlAdmin .= "/";

		}


		/**
		 * get DB
		 */
		public static function getDB(){

			if(empty(self::$db))
				self::$db = new UniteCreatorDB();

			return(self::$db);
		}

		/**
		 * check if some db table exists
		 */
		public static function isDBTableExists($tableName){

			global $wpdb;

			if(empty($tableName))
				UniteFunctionsUC::throwError("Empty table name!!!");

			$sql = "show tables like '$tableName'";

			$table = $wpdb->get_var($sql);

			if($table == $tableName)
				return (true);

			return (false);
		}

		/**
		 * add a prefix to the table name
		 */
		public static function prefixDBTable($tableName){

			global $wpdb;

			$tableRealName = $wpdb->prefix . $tableName;

			return $tableRealName;
		}

		/**
		 * get placeholders for values with the given format
		 */
		public static function getDBPlaceholders($values, $format){

			$placeholders = array();

			foreach($values as $value){
				$placeholders[] = $format;
			}

			$placeholders = implode(",", $placeholders);

			return $placeholders;
		}

		/**
		 * process the transaction
		 */
		public static function processDBTransaction($callback){

			global $wpdb;

			try{
				$wpdb->query("START TRANSACTION");

				$result = $callback();

				$wpdb->query("COMMIT");

				return $result;
			}catch(Exception $e){
				$wpdb->query("ROLLBACK");

				throw $e;
			}
		}

		/**
		 * get acf integrate object
		 */
		public static function getObjAcfIntegrate(){

			if(empty(self::$objAcfIntegrate))
				self::$objAcfIntegrate = new UniteCreatorAcfIntegrate();

			return(self::$objAcfIntegrate);
		}


		public static function a_________POSTS_TYPES________(){}

		/**
		 *
		 * return post type title from the post type
		 */
		public static function getPostTypeTitle($postType){

			$objType = get_post_type_object($postType);

			if(empty($objType))
				return($postType);

			$title = $objType->labels->singular_name;

			return($title);
		}


		/**
		 *
		 * get post type taxomonies
		 */
		public static function getPostTypeTaxomonies($postType){

			$arrTaxonomies = get_object_taxonomies(array( 'post_type' => $postType ), 'objects');

			$arrNames = array();
			foreach($arrTaxonomies as $key=>$objTax){
				$name = $objTax->labels->singular_name;
				if(empty($name))
					$name = $objTax->labels->name;

				$arrNames[$objTax->name] = $objTax->labels->singular_name;
			}

			return($arrNames);
		}

		/**
		 * get post edit link with elementor
		 */
		public static function getPostEditLink_editWithElementor($postID){

			$urlAdmin = admin_url("post.php");
			$urlAdmin .= "?post=$postID&action=elementor";

			return($urlAdmin);
		}

		/**
		 *
		 * get post types taxonomies as string
		 */
		public static function getPostTypeTaxonomiesString($postType){
			$arrTax = self::getPostTypeTaxomonies($postType);
			$strTax = "";
			foreach($arrTax as $name=>$title){
				if(!empty($strTax))
					$strTax .= ",";
				$strTax .= $name;
			}

			return($strTax);
		}

		/**
		 *
		 * get post types array with taxomonies
		 */
		public static function getPostTypesWithTaxomonies($filterPostTypes = array(), $fetchWithNoTax = true){

			$arrPostTypes = self::getPostTypesAssoc();

			$arrPostTypesOutput = array();

			foreach($arrPostTypes as $postType => $title){

				if(array_key_exists($postType, $filterPostTypes) == true)
					continue;

				$arrTaxomonies = self::getPostTypeTaxomonies($postType);

				if($fetchWithNoTax == false && empty($arrTaxomonies))
					continue;

				$arrType = array();
				$arrType["title"] = $title;
				$arrType["taxonomies"] = $arrTaxomonies;

				$arrPostTypesOutput[$postType] = $arrType;
			}


			return($arrPostTypesOutput);
		}


		/**
		 *
		 * get array of post types with categories (the taxonomies is between).
		 * get only those taxomonies that have some categories in it.
		 */
		public static function getPostTypesWithCats($arrFilterTypes = null){

			$arrPostTypes = self::getPostTypesWithTaxomonies();

			$arrOutput = array();
			foreach($arrPostTypes as $name => $arrPostType){

				if(array_key_exists($name, $arrFilterTypes) == true)
					continue;

				$arrTax = UniteFunctionsUC::getVal($arrPostType, "taxonomies");


				//collect categories
				$arrCats = array();
				foreach($arrTax as $taxName => $taxTitle){

					$cats = self::getCategoriesAssoc($taxName, false, $name);

					if(!empty($cats))
					foreach($cats as $catID=>$catTitle){

						if($taxName != "category"){
							$catID = $taxName."--".$catID;
							$catTitle = $catTitle." - [$taxTitle]";
						}

						$arrCats[$catID] = $catTitle;
					}
				}

				$arrPostType = array();
				$arrPostType["name"] = $name;
				$arrPostType["title"] = self::getPostTypeTitle($name);
				$arrPostType["cats"] = $arrCats;

				$arrOutput[$name] = $arrPostType;
			}


			return($arrOutput);
		}


		/**
		 *
		 * get array of post types with categories (the taxonomies is between).
		 * get only those taxomonies that have some categories in it.
		 */
		public static function getPostTypesWithCatIDs(){

			$arrTypes = self::getPostTypesWithCats();

			$arrOutput = array();

			foreach($arrTypes as $typeName => $arrType){

				$output = array();
				$output["name"] = $typeName;

				$typeTitle = self::getPostTypeTitle($typeName);

				//collect categories
				$arrCatsTotal = array();

				foreach($arrType as $arr){
					$cats = UniteFunctionsUC::getVal($arr, "cats");
					$catsIDs = array_keys($cats);
					$arrCatsTotal = array_merge($arrCatsTotal, $catsIDs);
				}

				$output["title"] = $typeTitle;
				$output["catids"] = $arrCatsTotal;

				$arrOutput[$typeName] = $output;
			}


			return($arrOutput);
		}



		/**
		 *
		 * get all the post types including custom ones
		 * the put to top items will be always in top (they must be in the list)
		 */
		public static function getPostTypesAssoc($arrPutToTop = array(), $isPublicOnly = false){

			$arrBuiltIn = array(
			 	"post"=>"post",
			 	"page"=>"page",
			 	"attachment"=>"attachment",
			 );

			 $arrCustomTypes = get_post_types(array('_builtin' => false));


			 //top items validation - add only items that in the customtypes list
			 $arrPutToTopUpdated = array();
			 foreach($arrPutToTop as $topItem){
			 	if(in_array($topItem, $arrCustomTypes) == true){
			 		$arrPutToTopUpdated[$topItem] = $topItem;
			 		unset($arrCustomTypes[$topItem]);
			 	}
			 }

			 $arrPostTypes = array_merge($arrPutToTopUpdated,$arrBuiltIn,$arrCustomTypes);

			 //update label
			 foreach($arrPostTypes as $key=>$type){
				$arrPostTypes[$key] = self::getPostTypeTitle($type);
			 }

			 //filter public only types
			 if($isPublicOnly == true)
			 	$arrPostTypes = self::filterPublicOnlyTypes($arrPostTypes);


			 return($arrPostTypes);
		}


		/**
		 * get public only types from post types array
		 */
		public static function filterPublicOnlyTypes($arrPostTypes){

			if(empty($arrPostTypes))
				return($arrPostTypes);

			foreach($arrPostTypes as $type => $typeTitle){

				if($type == "post" || $type == "page"){
					continue;
				}

				$objType = get_post_type_object($type);

				if(empty($objType))
					continue;

				if($objType->publicly_queryable == false)
					unset($arrPostTypes[$type]);
			}

			return($arrPostTypes);
		}


		public static function a_______TAXANOMIES_______(){}

		/**
		 * get term parent ids, including current term id
		 */
		public static function getTermParentIDs($objTerm){

			$currentTermID = $objTerm->term_id;

			$cacheKey = "term_".$currentTermID;

			if(isset(self::$cacheTermParents[$cacheKey]))
				return(self::$cacheTermParents[$cacheKey]);

			$arrCurrentIDs = array($currentTermID);

			if(!isset($objTerm->parent) || $objTerm->parent === 0){
				self::$cacheTermParents[$cacheKey] = $arrCurrentIDs;
				return($arrCurrentIDs);
			}

			$parents = get_ancestors( $currentTermID, $objTerm->taxonomy, 'taxonomy' );
			if(!empty($parents))
				$arrCurrentIDs = array_merge($arrCurrentIDs, $parents);

			self::$cacheTermParents[$cacheKey] = $arrCurrentIDs;

			return($arrCurrentIDs);
		}

		/**
		 * get term by slug
		 */
		public static function getTermBySlug($taxonomy, $slug){

			$args = array();
			$args["slug"] = $slug;
			$args["taxonomy"] = $taxonomy;
			$args["hide_empty"] = false;

			$arrTerms = get_terms($args);

			if(empty($arrTerms))
				return(null);

			$term = $arrTerms[0];

			return($term);
		}


		/**
		 * get term data
		 */
		public static function getTermData($term){

			$data = array();
			$data["term_id"] = $term->term_id;
			$data["name"] = $term->name;
			$data["slug"] = $term->slug;
			$data["description"] = $term->description;
			$data["taxonomy"] = $term->taxonomy;

			if(isset($term->parent))
				$data["parent_id"] = $term->parent;

			$count = "";

			if(isset($term->count))
				$count = $term->count;

			$data["count"] = $count;

			//get link
			$link = get_term_link($term);
			$data["link"] = $link;

			return($data);
		}

		/**
		 * convert terms objects to data
		 */
		public static function getTermsObjectsData($arrTerms, $taxonomyName, $currentTermID = null){

			if(empty($currentTermID))
				$currentTermID = self::getCurrentTermID();

			$arrTermData = array();

			if(empty($arrTerms))
				return(array());

			$counter = 0;
			foreach($arrTerms as $term){

				$termData = self::getTermData($term);

				$current = false;
				if($termData["term_id"] == $currentTermID)
					$current = true;

				$termData["iscurrent"] = $current;

				$slug = $termData["slug"];
				if(empty($slug))
					$slug = "{$taxonomyName}_{$counter}";

				$arrTermData[$slug] = $termData;
			}

			return($arrTermData);
		}

		/**
		 * get current term ID
		 */
		public static function getCurrentTermID(){

			$term = get_queried_object();
			if(empty($term))
				return(null);

			if(!isset($term->term_id))
				return(null);

			return($term->term_id);
		}

		/**
		 * filter term objects by slugs
		 */
		public static function getTerms_filterBySlugs($arrTermObjects, $arrSlugs){

			if(empty($arrTermObjects))
				return($arrTermObjects);

			$arrSlugsAssoc = UniteFunctionsUC::arrayToAssoc($arrSlugs);
			$arrTermsNew = array();
			foreach($arrTermObjects as $term){

				if(isset($arrSlugsAssoc[$term->slug]))
					continue;

				$arrTermsNew[] = $term;
			}


			return($arrTermsNew);
		}

		/**
		 * get terms arguments
		 */
		public static function getTermsArgs($taxonomy, $orderBy = null, $orderDir = null, $hideEmpty = false, $addArgs = null){

			$hideEmpty = UniteFunctionsUC::strToBool($hideEmpty);

			$args = array();
			$args["hide_empty"] = $hideEmpty;
			$args["taxonomy"] = $taxonomy;
			$args["count"] = true;
			$args["number"] = 5000;

			if(!empty($orderBy)){
				$args["orderby"] = $orderBy;

				if(empty($orderDir))
					$orderDir = self::ORDER_DIRECTION_ASC;

				$args["order"] = $orderDir;
			}

			if(is_array($addArgs))
				$args = $args + $addArgs;

			self::$arrLastTermsArgs = $args;

			return($args);
		}

		/**
		 * get terms
		 */
		public static function getTerms($taxonomy, $orderBy = null, $orderDir = null, $hideEmpty = false, $arrExcludeSlugs = null, $addArgs = null){

			$currentTermID = self::getCurrentTermID();

			$args = self::getTermsArgs($taxonomy, $orderBy, $orderDir, $hideEmpty, $addArgs);

			HelperUC::addDebug("Terms Query", $args);

			$arrTermsObjects = get_terms($args);

			if(!empty($arrExcludeSlugs)){
				HelperUC::addDebug("Terms Before Filter:", $arrTermsObjects);
				HelperUC::addDebug("Exclude by:", $arrExcludeSlugs);
			}

			if(!empty($arrExcludeSlugs) && is_array($arrExcludeSlugs))
				$arrTermsObjects = self::getTerms_filterBySlugs($arrTermsObjects, $arrExcludeSlugs);

			$arrTerms = self::getTermsObjectsData($arrTermsObjects, $taxonomy, $currentTermID);

			return($arrTerms);

		}

		/**
		 * get specific terms
		 */
		public static function getSpecificTerms($slugs, $taxonomy){

			$currentTermID = self::getCurrentTermID();

			if(is_string($slugs)){

				$slugs = trim($slugs);
				if(empty($slugs))
					return(array());

				$slugs = explode(",", $slugs);
			}

			if(!is_array($slugs))
				return(array());

			if(empty($slugs))
				return(array());

			$args = array();
			$args["slug"] = $slugs;

			HelperUC::addDebug("Terms Args", $args);

			$arrTermsObjects = get_terms($args);

			$arrTerms = self::getTermsObjectsData($arrTermsObjects, $taxonomy, $currentTermID);

			return($arrTerms);
		}


		/**
		 * get all post terms
		 */
		public static function getPostAllSingleTerms($postID){
			
			if(is_numeric($postID) == true)
				$post = get_post($postID);
			else
				$post = $postID;
			
			$postType = $post->post_type;
				
			$arrTaxonomies = self::getPostTypeTaxomonies($postType);
			
			if(empty($arrTaxonomies))
				return(array());
			
			$arrAllTerms = array();
				
			foreach($arrTaxonomies as $taxName => $taxTitle){
				
				$arrTerms = self::getPostSingleTerms($postID, $taxName);
				
				if(empty($arrTerms))
					continue;
				
				$arrAllTerms += $arrTerms;
			}
				
			
			return($arrAllTerms);
		}
		
		
		/**
		 * get post single taxonomy terms
		 */
		public static function getPostSingleTerms($postID, $taxonomyName){

			//check from cache
			if(isset(GlobalsProviderUC::$arrPostTermsCache[$postID][$taxonomyName])){

				$arrTerms = GlobalsProviderUC::$arrPostTermsCache[$postID][$taxonomyName];

				$arrTerms = array_values($arrTerms);
			}else{

				$arrTerms = wp_get_post_terms($postID, $taxonomyName);

				if(is_wp_error($arrTerms)){

					$errorMessage = "get terms error: post: $postID , tax: $taxonomyName |". $arrTerms->get_error_message();
					UniteFunctionsUC::throwError($errorMessage);
				}

			}

			$arrTerms = self::getTermsObjectsData($arrTerms, $taxonomyName);

			return($arrTerms);
		}

		/**
		 * get post single taxonomy terms
		 */
		public static function getPostSingleTermsTitles($postID, $taxonomyName){

			$arrTerms = self::getPostSingleTerms($postID, $taxonomyName);
			if(empty($arrTerms))
				return(array());

			$output = UniteFunctionsUC::assocToArrayNames($arrTerms, "name");

			return($output);
		}


		/**
		 * get post terms with all taxonomies
		 */
		public static function getPostTerms($post){

			if(empty($post))
				return(array());

			$postType = $post->post_type;
			$postID = $post->ID;

			if(empty($postID))
				return(array());

			//option 'objects' also available
			$arrTaxonomies = self::getPostTypeTaxomonies($postType);

			if(empty($arrTaxonomies))
				return(array());

			$arrDataOutput = array();

			foreach($arrTaxonomies as $taxName => $taxTitle){

				$arrTerms = wp_get_post_terms($postID, $taxName);

				$arrTermsData = self::getTermsObjectsData($arrTerms, $taxName);

				$arrDataOutput[$taxName] = $arrTermsData;
			}


			return($arrDataOutput);
		}

		/**
		 * get post term
		 */
		public static function getPostTerm($postID, $taxName, $termSlug){

			$arrTerms = wp_get_post_terms($postID, $taxName);

			if(empty($arrTerms))
				return(null);

			foreach($arrTerms as $term){

				$slug = $term->slug;

				if($slug != $termSlug)
					continue;

				$termData = self::getTermData($term);

				return($termData);
			}

			return(null);
		}

		/**
		 * get post terms title string
		 */
		public static function getPostTermsTitlesString($post, $withTax = false){

			if(is_numeric($post))
				$post = get_post($post);

			$arrTerms = self::getPostTermsTitles($post, $withTax);

			if(empty($arrTerms))
				return("");

			$strTerms = implode(", ", $arrTerms);

			return($strTerms);
		}

		/**
		 * get post terms titles
		 */
		public static function getPostTermsTitles($post, $withTax = false){

			$arrTermsWithTax = self::getPostTerms($post);

			if(empty($arrTermsWithTax))
				return(array());

			$arrTitles = array();

			foreach($arrTermsWithTax as $taxanomy=>$arrTerms){

				if(empty($arrTerms))
					continue;

				foreach($arrTerms as $term){

					$name = UniteFunctionsUC::getVal($term, "name");

					if($withTax == true){
						$taxonomy = UniteFunctionsUC::getVal($term, "taxonomy");

						if(!empty($taxanomy) && $taxanomy != "category")
							$name .= "($taxanomy)";
					}

					if(empty($name))
						continue;

					$arrTitles[] = $name;
				}
			}

			return($arrTitles);
		}

		/**
		 * get post terms id's
		 * if empty - get current post term id's
		 */
		public static function getPostTermIDs($post = null){

			if(empty($post))
				$post = get_post();

			if(empty($post))
				return(array());

			$arrTermsWithTax = self::getPostTerms($post);

			if(empty($arrTermsWithTax))
				return(array());

			$arrTermIDs = array();

			foreach($arrTermsWithTax as $terms){

				if(empty($terms))
					continue;

				foreach($terms as $term){
					$termID = UniteFunctionsUC::getVal($term, "term_id");
					$arrTermIDs[] = $termID;
				}

			}

			return($arrTermIDs);
		}


		/**
		 *
		 * get assoc list of the taxonomies
		 */
		public static function getTaxonomiesAssoc(){
			$arr = get_taxonomies();

			unset($arr["post_tag"]);
			unset($arr["nav_menu"]);
			unset($arr["link_category"]);
			unset($arr["post_format"]);

			return($arr);
		}



		/**
		 *
		 * get array of all taxonomies with categories.
		 */
		public static function getTaxonomiesWithCats(){

			if(!empty(self::$arrTaxCache))
				return(self::$arrTaxCache);

			$arrTax = self::getTaxonomiesAssoc();

			$arrTaxNew = array();
			foreach($arrTax as $key => $value){

				$arrItem = array();
				$arrItem["name"] = $key;
				$arrItem["title"] = $value;
				$arrItem["cats"] = self::getCategoriesAssoc($key);
				$arrTaxNew[$key] = $arrItem;
			}

			self::$arrTaxCache = $arrTaxNew;

			return($arrTaxNew);
		}

		/**
		 * update terms counts (indexes)
		 */
		public static function updateTermsIndexes(){

			$db = HelperUC::getDB();

			$tableTerms = GlobalsUC::$table_prefix."term_taxonomy";

			$arrTerms = $db->fetch($tableTerms);

			$arrTax = array();

			foreach($arrTerms as $term){

				$termID = UniteFunctionsUC::getVal($term, "term_id");
				$taxonomy = UniteFunctionsUC::getVal($term, "taxonomy");

				if(strpos($taxonomy, "translation_") !== false)
					continue;

				if(strpos($taxonomy, "elementor_") !== false)
					continue;

				if(!isset($arrTax[$taxonomy]))
					$arrTax[$taxonomy] = array();

				$arrTax[$taxonomy][] = $termID;
			}

			//do the update count
			foreach($arrTax as $taxonomy=>$arrTerms){
				@wp_update_term_count_now( $arrTerms, $taxonomy);
			}

		}

	/**
	 * get current tax query
	 */
	public static function getCurrentPageTaxQuery(){

		global $wp_query;

		if(empty($wp_query))
			return(null);

		$taxQuery = $wp_query->tax_query;

		if(empty($taxQuery))
			return(null);

		$queries = $taxQuery->queries;

		if(empty($queries))
			return(null);

		return($queries);
	}

	
		
	/**
	 * set arguments tax query, merge with existing if avaliable
	 */
	public static function mergeArgsTaxQuery($args, $arrTaxQuery){

		if(empty($arrTaxQuery))
			return ($args);

		$existingTaxQuery = UniteFunctionsUC::getVal($args, "tax_query");

		if(empty($existingTaxQuery)){
			$args["tax_query"] = $arrTaxQuery;

			return ($args);
		}

		$newTaxQuery = array(
			$existingTaxQuery,
			$arrTaxQuery
		);

		$newTaxQuery["relation"] = "AND";

		$args["tax_query"] = $newTaxQuery;

		return ($args);
	}


		public static function a_________TAXONOMY_LEVELS___________(){}

		/**
		 * filter last level terms only from terms list
		 */
		public static function filterTermsLastLevel($arrTerms, $taxonomy){

			if(empty($arrTerms))
				return($arrTerms);

			if(count($arrTerms) == 1)
				return($arrTerms);

			//get parents list
			$arrParents = array();
			foreach($arrTerms as $term){

				$parentID = UniteFunctionsUC::getVal($term, "parent_id");

				$arrParents["term_".$parentID] = true;

			}

			//same parent

			if(count($arrParents) == 1)
				return($arrTerms);

			//return not main if main exists, and there is only 2

			$mainTerm = UniteFunctionsUC::getVal($arrParents, "term_0");

			if(count($arrParents) == 2 && !empty($mainTerm)){

				$arrOutput = array();

				foreach($arrTerms as $term){
					$parentID = UniteFunctionsUC::getVal($term, "parent_id");

					if(empty($parentID))
						continue;

					$arrOutput[] = $term;
				}

				return($arrOutput);
			}

			//get by hierarchy

			$arrTerms = self::addTermsLevels($arrTerms, $taxonomy);

			//find max level

			$maxLevel = 0;
			foreach($arrTerms as $term){

				$level = UniteFunctionsUC::getVal($term, "level");

				if($level > $maxLevel)
					$maxLevel = $level;
			}


			//filter by last only
			$arrOutput = array();

			foreach($arrTerms as $term){

				$level = UniteFunctionsUC::getVal($term, "level");

				if($level == $maxLevel)
					$arrOutput[] = $term;
			}


			return($arrOutput);
		}


		/**
		 * add levels to terms
		 */
		public static function addTermsLevels($arrTerms, $taxonomy){

			//add level to terms
			$arrParentIDs = self::getTermsIDsWithParentIDs($taxonomy);

			foreach($arrTerms as $key=>$term){

				$termID = UniteFunctionsUC::getVal($term, "term_id");

				$level = self::getTermLevel($termID, $arrParentIDs);

				$term["level"] = $level;

				$arrTerms[$key] = $term;
			}

			return($arrTerms);
		}


		/**
		 * get term level
		 */
		private static function getTermLevel($termID, $arrParentIDs){

			$level = 0;

			do{
				$termID = UniteFunctionsUC::getVal($arrParentIDs, $termID);

				$isFound = !empty($termID);

				if($isFound == true)
					$level++;

			}while($isFound);

			return($level);
		}


		/**
		 * get term hierarchy level
		 */
		public static function getTermsIDsWithParentIDs($taxonomy){

			//get from cache

			$arrParentsCache = UniteFunctionsUC::getVal(self::$arrTermParentsCache, $taxonomy);

			if(!empty($arrParentsCache))
				return($arrParentsCache);

			$arrHierarchy = _get_term_hierarchy($taxonomy);

			if(empty($arrHierarchy))
				return(array());

			$arrTermIDs = array();

			foreach($arrHierarchy as $parentID => $arrIDs){

				foreach($arrIDs as $termID)
					$arrTermIDs[$termID] = $parentID;
			}


			//add cache
			self::$arrTermParentsCache[$taxonomy] = $arrTermIDs;

			return($arrTermIDs);
		}



		public static function a_________CATEGORIES_AND_TAGS___________(){}



		/**
		 * check if category not exists and add it, return catID anyway
		 */
		public static function addCategory($catName){

			$catID = self::getCatIDByTitle($catName);
			if(!empty($catID))
				return($catID);

			$arrCat = array(
			  'cat_name' => $catName
			);

			$catID = wp_insert_category($arrCat);
			if($catID == false)
				UniteFunctionsUC::throwError("category: $catName don't created");

			return($catID);
		}


		/**
		 *
		 * get the category data
		 */
		public static function getCategoryData($catID){
			$catData = get_category($catID);
			if(empty($catData))
				return($catData);

			$catData = (array)$catData;
			return($catData);
		}


		/**
		 *
		 * get post categories by postID and taxonomies
		 * the postID can be post object or array too
		 */
		public static function getPostCategoriesIDs($post){

			if(empty($post))
				return(array());

			$postType = $post->post_type;

			$taxonomy = "category";

			switch($postType){
				case "post":
				case "page":
					$taxonomy = "category";
				break;
				case "product":
					$taxonomy = "product_category";
				break;
			}

			$arrCatIDs = wp_get_post_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ));

			return($arrCatIDs);
		}

		/**
		 *
		 * get post categories by postID and taxonomies
		 * the postID can be post object or array too
		 */
		public static function getPostTagsIDs($post){

			if(empty($post))
				return(array());

			$postType = $post->post_type;

			$taxonomy = "category";

			switch($postType){
				case "post":
				case "page":
					$taxonomy = "post_tag";
				break;
				case "product":
					$taxonomy = "product_tag";
				break;
			}

			$arrTagsIDs = wp_get_post_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ));

			return($arrTagsIDs);
		}


		/**
		 *
		 * get post categories list assoc - id / title
		 */
		public static function getCategoriesAssoc($taxonomy = "category", $addNotSelected = false, $forPostType = null){

			if($taxonomy === null)
				$taxonomy = "category";

			$arrCats = array();

			if($addNotSelected == true)
				$arrCats["all"] = esc_html__("[All Categories]", "unlimited-elements-for-elementor");

			if(strpos($taxonomy,",") !== false){
				$arrTax = explode(",", $taxonomy);
				foreach($arrTax as $tax){
					$cats = self::getCategoriesAssoc($tax);
					$arrCats = array_merge($arrCats,$cats);
				}

				return($arrCats);
			}

			$args = array("taxonomy"=>$taxonomy);
			$args["hide_empty"] = false;
			$args["number"] = 5000;

			$cats = get_categories($args);

			foreach($cats as $cat){

				$numItems = $cat->count;
				$itemsName = "items";
				if($numItems == 1)
					$itemsName = "item";

				$title = $cat->name . " ($numItems $itemsName)";

				$id = $cat->cat_ID;
				$arrCats[$id] = $title;
			}
			return($arrCats);
		}

		/**
		 *
		 * get categories by id's
		 */
		public static function getCategoriesByIDs($arrIDs,$strTax = null){

			if(empty($arrIDs))
				return(array());

			if(is_string($arrIDs))
				$strIDs = $arrIDs;
			else
				$strIDs = implode(",", $arrIDs);

			$args = array();
			$args["include"] = $strIDs;

			if(!empty($strTax)){
				if(is_string($strTax))
					$strTax = explode(",",$strTax);

				$args["taxonomy"] = $strTax;
			}

			$arrCats = get_categories( $args );

			if(!empty($arrCats))
				$arrCats = UniteFunctionsUC::convertStdClassToArray($arrCats);

			return($arrCats);
		}


		/**
		 *
		 * get categories short
		 */
		public static function getCategoriesByIDsShort($arrIDs,$strTax = null){
			$arrCats = self::getCategoriesByIDs($arrIDs,$strTax);
			$arrNew = array();
			foreach($arrCats as $cat){
				$catID = $cat["term_id"];
				$catName = $cat["name"];
				$arrNew[$catID] =  $catName;
			}

			return($arrNew);
		}




		/**
		 *
		 * get post tags html list
		 */
		public static function getTagsHtmlList($postID,$before="",$sap=",",$after=""){

			$tagList = get_the_tag_list($before,",",$after,$postID);

			return($tagList);
		}


		/**
		 * get category by slug name
		 */
		public static function getCatIDBySlug($slug, $type = "slug"){

			$arrCats = get_categories(array("hide_empty"=>false));

			foreach($arrCats as $cat){
				$cat = (array)$cat;

				switch($type){
					case "slug":
						$catSlug = $cat["slug"];
					break;
					case "title":
						$catSlug = $cat["name"];
					break;
					default:
						UniteFunctionsUC::throwError("Wrong cat name");
					break;
				}

				$catID = $cat["term_id"];

				if($catSlug == $slug)
					return($catID);
			}

			return(null);
		}

		/**
		 * get category by title (name)
		 */
		public static function getCatIDByTitle($title){

			$catID = self::getCatIDBySlug($title,"title");

			return($catID);
		}

		public static function a________GENERAL_GETTERS________(){}


		/**
		 *
		 * get sort by with the names
		 */
		public static function getArrSortBy($isForWoo = false, $forFilter = false){

			$arr = array();
			$arr["default"] = __("Default", "unlimited-elements-for-elementor");

			if($forFilter == true){
				$arr["meta"] = __("Meta Field", "unlimited-elements-for-elementor");
			}

			$postid = self::SORTBY_ID;
			if($forFilter == true)
				$postid = "id";

			$arr[$postid] = __("Post ID", "unlimited-elements-for-elementor");

			$arr[self::SORTBY_DATE] = __("Date", "unlimited-elements-for-elementor");
			$arr[self::SORTBY_TITLE] = __("Title", "unlimited-elements-for-elementor");

			if($isForWoo == true){
				$arr[self::SORTBY_PRICE] = __("Price (WooCommerce)", "unlimited-elements-for-elementor");
				$arr[self::SORTBY_SALE_PRICE] = __("Sale Price (WooCommerce)", "unlimited-elements-for-elementor");
				$arr[self::SORTBY_SALES] = __("Number of Sales (WooCommerce)", "unlimited-elements-for-elementor");
				$arr[self::SORTBY_RATING] = __("Rating (WooCommerce)", "unlimited-elements-for-elementor");
			}

			$arr[self::SORTBY_SLUG] = __("Slug", "unlimited-elements-for-elementor");
			$arr[self::SORTBY_AUTHOR] = __("Author", "unlimited-elements-for-elementor");
			$arr[self::SORTBY_LAST_MODIFIED] = __("Last Modified", "unlimited-elements-for-elementor");
			$arr[self::SORTBY_COMMENT_COUNT] = __("Number Of Comments", "unlimited-elements-for-elementor");
			$arr[self::SORTBY_RAND] = __("Random", "unlimited-elements-for-elementor");
			$arr[self::SORTBY_NONE] = __("Unsorted", "unlimited-elements-for-elementor");
			$arr[self::SORTBY_MENU_ORDER] = __("Menu Order", "unlimited-elements-for-elementor");
			$arr[self::SORTBY_PARENT] = __("Parent Post", "unlimited-elements-for-elementor");

			if($forFilter !== true){

				$arr["post__in"] = __("Preserve Posts In Order", "unlimited-elements-for-elementor");

				$arr[self::SORTBY_META_VALUE] = __("Meta Field Value", "unlimited-elements-for-elementor");
				$arr[self::SORTBY_META_VALUE_NUM] = __("Meta Field Value (numeric)", "unlimited-elements-for-elementor");
			}

			return($arr);
		}


		/**
		 *
		 * get array of sort direction
		 */
		public static function getArrSortDirection(){

			$arr = array();
			$arr["default"] = __("Default", "unlimited-elements-for-elementor");
			$arr[self::ORDER_DIRECTION_DESC] = __("Descending", "unlimited-elements-for-elementor");
			$arr[self::ORDER_DIRECTION_ASC] = __("Ascending", "unlimited-elements-for-elementor");

			return($arr);
		}

		/**
		 * get sort by term
		 */
		public static function getArrTermSortBy(){

			$arr = array();
			$arr["default"] = __("Default", "unlimited-elements-for-elementor");
			$arr["name"] = __("Name", "unlimited-elements-for-elementor");
			$arr["slug"] = __("Slug", "unlimited-elements-for-elementor");
			$arr["term_group"] = __("Term Group", "unlimited-elements-for-elementor");
			$arr["term_id"] = __("Term ID", "unlimited-elements-for-elementor");
			$arr["description"] = __("Description", "unlimited-elements-for-elementor");
			$arr["parent"] = __("Parent", "unlimited-elements-for-elementor");
			$arr["count"] = __("Count - (number of posts associated)", "unlimited-elements-for-elementor");

			return($arr);
		}

		private function a_______CUSTOM_FIELDS________(){}


		/**
		 * get keys of acf fields
		 */
		public static function getAcfFieldsKeys($postID, $objName = "post", $addPrefix = true){

			$objAcf = self::getObjAcfIntegrate();

			$arrKeys = $objAcf->getAcfFieldsKeys($postID, $objName, $addPrefix);

			return($arrKeys);
		}


		/**
		 * get term custom field
		 */
		public static function getTermCustomFields($termID, $addPrefixes = true){

			$cacheKey = $termID;
			if($addPrefixes == true)
				$cacheKey = $termID."_prefixes";

			if(isset(self::$cacheTermCustomFields[$cacheKey]))
				return(self::$cacheTermCustomFields[$cacheKey]);

			$arrMeta = self::getTermMeta($termID, $addPrefixes);

			if(empty($arrMeta))
				return(array());

			$isAcfActive = UniteCreatorAcfIntegrate::isAcfActive();

			if($isAcfActive == false)
				return($arrMeta);

			//merge with acf

			$objAcf = self::getObjAcfIntegrate();
			$arrCustomFields = $objAcf->getAcfFields($termID, "term",$addPrefixes);

			if(!empty($arrCustomFields))
				$arrMeta = array_merge($arrMeta, $arrCustomFields);

			self::$cacheTermCustomFields[$cacheKey] = $arrMeta;

			return($arrMeta);
		}
		
		/**
		 * get post custom field
		 */
		public static function getTermCustomField($termID, $name){

			if(empty($name))
				return("");
				
			$isAcfActive = UniteCreatorAcfIntegrate::isAcfActive();

			$value = "";
			
			if($isAcfActive == true){

				$objAcf = self::getObjAcfIntegrate();
				$value = $objAcf->getAcfFieldValue($name, $termID,"term");

				if(empty($value))
					$value = get_term_meta($termID, $name, true);
			}else
				$value = get_term_meta($termID, $name, true);
			 
			if(is_array($value))
				$value = json_encode($value);
			
			$value = trim($value);

			return($value);
		}
		

		/**
		 * get post custom field
		 */
		public static function getPostCustomField($postID, $name){

			if(empty($name))
				return("");

			$isAcfActive = UniteCreatorAcfIntegrate::isAcfActive();

			$value = "";

			if($isAcfActive == true){

				$objAcf = self::getObjAcfIntegrate();
				$value = $objAcf->getAcfFieldValue($name, $postID);

				if(empty($value))
					$value = get_post_meta($postID, $name, true);
			}else
				$value = get_post_meta($postID, $name, true);

			if(is_array($value))
				$value = json_encode($value);

			$value = trim($value);

			return($value);
		}


		/**
		 * get post custom fields
		 * including acf
		 */
		public static function getPostCustomFields($postID, $addPrefixes = true, $imageSize = null){

			$prefix = null;
			if($addPrefixes == true)
				$prefix = "cf_";

			$isAcfActive = UniteCreatorAcfIntegrate::isAcfActive();

			//get acf
			if($isAcfActive){

				$objAcf = self::getObjAcfIntegrate();
				$arrCustomFields = $objAcf->getAcfFields($postID, "post", $addPrefixes, $imageSize);

				//if emtpy - get from regular meta
				if(empty($arrCustomFields))
					$arrCustomFields = self::getPostMeta($postID, false, $prefix);


			}else{		//without acf - get regular custom fields

				$arrCustomFields = null;

				$isPodsExists = UniteCreatorPodsIntegrate::isPodsExists();
				if($isPodsExists){
					$objPods = UniteCreatorPodsIntegrate::getObjPodsIntegrate();
					$arrCustomFields = $objPods->getPodsFields($postID, $addPrefixes);
				}

				//handle toolset
				$isToolsetActive = UniteCreatorToolsetIntegrate::isToolsetExists();

				if($isToolsetActive == true && empty($arrCustomFields)){
					$objToolset = new UniteCreatorToolsetIntegrate();

					$arrCustomFields = $objToolset->getPostFieldsWidthData($postID);
				}

				if(empty($arrCustomFields))
					$arrCustomFields = self::getPostMeta($postID, false, $prefix);

			}

			if(empty($arrCustomFields)){
				$arrCustomFields = array();
				return($arrCustomFields);
			}


			return($arrCustomFields);
		}


		/**
		 * get post meta data
		 */
		public static function getPostMeta($postID, $getSystemVars = true, $prefix = null){

			$arrMeta = get_post_meta($postID);
			$arrMetaOutput = array();

			foreach($arrMeta as $key=>$item){

				//filter by key
				if($getSystemVars == false){
					$firstSign = $key[0];

					if($firstSign == "_")
						continue;
				}

				if(!empty($prefix))
					$key = $prefix.$key;

				if(is_array($item) && count($item) == 1)
					$item = $item[0];

				$arrMetaOutput[$key] = $item;
			}


			return($arrMetaOutput);
		}


		/**
		 * get terms meta
		 */
		public static function getTermMeta($termID, $addPrefixes = false){

			$arrMeta = get_term_meta($termID);

			if(empty($arrMeta))
				return(array());

			$arrMetaOutput = array();

			foreach($arrMeta as $key=>$item){

				if(is_array($item) && count($item) == 1)
					$item = $item[0];

				if($addPrefixes == true)
					$key = "cf_".$key;

				$arrMetaOutput[$key] = $item;
			}

			return($arrMetaOutput);
		}

		/**
		 * get term meta
		 */
		public static function getTermImage($termID, $metaKey){

			if(empty($termID) || $termID === "current")
				$termID = self::getCurrentTermID();

			if(empty($termID))
				return(null);

		if($metaKey == "debug"){
			$arrMeta = get_term_meta($termID);

			dmp("term: $termID meta: ");

			dmp($arrMeta);
		}

		if(empty($metaKey))
			return (null);

		$attachmentID = get_term_meta($termID, $metaKey, true);

		if(empty($attachmentID))
			return (null);

		$arrImage = self::getAttachmentData($attachmentID);

		return ($arrImage);
	}

	/**
	 * get term meta
	 */
	public static function getPostImage($postID, $metaKey){

		if(empty($postID) || $postID === "current")
			$postID = get_post();

		if(empty($postID))
			return (null);

		if($metaKey == "debug"){
			$arrMeta = get_post_meta($postID);

			dmp("post: $postID meta: ");

			dmp($arrMeta);
		}

		if(empty($metaKey))
			return (null);

		$attachmentID = get_post_meta($postID, $metaKey, true);

		if(empty($attachmentID))
			return (null);

		$arrImage = self::getAttachmentData($attachmentID);

		return ($arrImage);
	}

	/**
	 * get pods meta keys
	 */
	public static function getPostMetaKeys_PODS($postID){

		$isPodsExists = UniteCreatorPodsIntegrate::isPodsExists();

		if($isPodsExists == false)
			return (array());

		$objPods = UniteCreatorPodsIntegrate::getObjPodsIntegrate();
		$arrCustomFields = $objPods->getPodsFields($postID);

		if(empty($arrCustomFields))
			return (array());

		$arrMetaKeys = array_keys($arrCustomFields);

		return ($arrMetaKeys);
	}

	/**
	 * get post meta keys
	 */
	public static function getPostMetaKeys_TOOLSET($postID){

		$isToolsetExists = UniteCreatorToolsetIntegrate::isToolsetExists();
		if($isToolsetExists == false)
			return (array());

		$objToolset = new UniteCreatorToolsetIntegrate();
		$arrFieldsKeys = $objToolset->getPostFieldsKeys($postID);
		if(empty($arrFieldsKeys))
			return ($arrFieldsKeys);

		return ($arrFieldsKeys);
	}

	/**
	 * get post meta data
	 */
	public static function getPostMetaKeys($postID, $prefix = null, $includeUnderscore = false){

		$postMeta = get_post_meta($postID);

		if(empty($postMeta))
			return (array());

		$arrMetaKeys = array_keys($postMeta);

		$arrKeysOutput = array();
		foreach($arrMetaKeys as $key){
			$firstSign = $key[0];

			if($firstSign == "_" && $includeUnderscore == false)
				continue;

			if(!empty($prefix))
				$key = $prefix . $key;

			$arrKeysOutput[] = $key;
		}

		return ($arrKeysOutput);
	}

	/**
	 * get term custom field
	 */
	public static function getUserCustomFields($userID, $addPrefixes = true){

		$cacheKey = $userID;
		if($addPrefixes == true)
			$cacheKey = $userID . "_prefixes";

		if(isset(self::$cacheUserCustomFields[$cacheKey]))
			return (self::$cacheUserCustomFields[$cacheKey]);

		$isAcfActive = UniteCreatorAcfIntegrate::isAcfActive();

		if($isAcfActive == false){
			$arrMeta = self::getUserMeta($userID, array(), $addPrefixes);

			return ($arrMeta);
		}

		$objAcf = self::getObjAcfIntegrate();
		$arrCustomFields = $objAcf->getAcfFields($userID, "user", $addPrefixes);

		self::$cacheUserCustomFields[$cacheKey] = $arrCustomFields;

		return ($arrCustomFields);
	}

	public static function a__________POST_GETTERS__________(){	}

	/**
	 *
	 * get single post
	 */
	public static function getPost($postID, $addAttachmentImage = false, $getMeta = false){

		$post = get_post($postID);
		if(empty($post))
			UniteFunctionsUC::throwError("Post with id: $postID not found");

		$arrPost = $post->to_array();

		if($addAttachmentImage == true){
			$arrImage = self::getPostAttachmentImage($postID);
			if(!empty($arrImage))
				$arrPost["image"] = $arrImage;
		}

		if($getMeta == true)
			$arrPost["meta"] = self::getPostMeta($postID);

		return ($arrPost);
	}

	/**
	 * get post by name
	 */
	public static function getPostByName($name, $postType = null){

		if(!empty($postType)){
			$query = array(
				'name' => $name,
				'post_type' => $postType,
			);

			$arrPosts = get_posts($query);
			$post = $arrPosts[0];

			return ($post);
		}

		//get only by name
		$postID = self::getPostIDByPostName($name);
		if(empty($postID))
			return (null);

		$post = get_post($postID);

		return ($post);
	}

	/**
	 * get post children
	 */
	public static function getPostChildren($post){

		if(empty($post))
			return (array());

		$args = array();
		$args["post_parent"] = $post->ID;
		$args["post_type"] = $post->post_type;

		$arrPosts = get_posts($args);

		return ($arrPosts);
	}

	/**
	 * get post id by post name
	 */
	public static function getPostIDByPostName($postName){

		$tablePosts = UniteProviderFunctionsUC::$tablePosts;

		$db = self::getDB();
		$response = $db->fetch($tablePosts, array("post_name" => $postName));

		if(empty($response))
			return (null);

		$postID = $response[0]["ID"];

		return ($postID);
	}

	/**
	 * get post id by name, using DB
	 */
	public static function isPostNameExists($postName){

		$tablePosts = UniteProviderFunctionsUC::$tablePosts;

		$db = self::getDB();
		$response = $db->fetch($tablePosts, array("post_name" => $postName));

		$isExists = !empty($response);

		return ($isExists);
	}

	/**
	 * where filter, add the search query
	 */
	public static function getPosts_whereFilter($where, $wp_query){

		global $wpdb;

		$arrQuery = $wp_query->query;
		$titleFilter = UniteFunctionsUC::getVal($arrQuery, "title_filter");

		if(!empty($titleFilter)){
			if(!empty($where))
				$where .= " AND";

			$where .= " wp_posts.post_title like '%$titleFilter%'";
		}

		return ($where);
	}

	/**
	 *
	 * get posts post type
	 */
	public static function getPostsByType($postType, $sortBy = self::SORTBY_TITLE, $addParams = array(), $returnPure = false){

		if(empty($postType))
			$postType = "any";

		$query = array(
			'post_type' => $postType,
			'orderby' => $sortBy,
		);

		if($sortBy == self::SORTBY_MENU_ORDER)
			$query["order"] = self::ORDER_DIRECTION_ASC;

		$query["posts_per_page"] = 2000;  //no limit

		if(!empty($addParams))
			$query = array_merge($query, $addParams);

		$titleFilter = UniteFunctionsUC::getVal($query, "title_filter");
		if(!empty($titleFilter)){
			$query["suppress_filters"] = false;  //no limit
			add_filter('posts_where', array("UniteFunctionsWPUC", "getPosts_whereFilter"), 10, 2);
		}

		$arrPosts = get_posts($query);

		if(!empty($titleFilter))
			remove_filter("posts_where", array("UniteFunctionsWPUC", "getPosts_whereFilter"));

		if($returnPure == true)
			return ($arrPosts);

		foreach($arrPosts as $key => $post){
			if(method_exists($post, "to_array"))
				$arrPost = $post->to_array();
			else
				$arrPost = (array)$post;

			$arrPosts[$key] = $arrPost;
		}

		return ($arrPosts);
	}

	/**
	 * get tax query from a gived category
	 */
	private static function getPosts_getTaxQuery_getArrQuery($arrQuery, $category, $categoryRelation, $isIncludeChildren, $isExclude){

		if($isIncludeChildren !== true)
			$isIncludeChildren = false;

		if(is_array($category))
			$arrCategories = $category;
		else
			$arrCategories = explode(",", $category);

		foreach($arrCategories as $cat){
			//check for empty category - mean all categories
			if($cat == "all" || empty($cat))
				continue;

			//set taxanomy name
			$taxName = "category";
			$catID = $cat;

			if(is_numeric($cat) == false){
				$arrTax = explode("--", $cat);
				if(count($arrTax) == 2){
					$taxName = $arrTax[0];
					$catID = $arrTax[1];
				}
			}

			//add the search item

			$field = "id";
			if(is_numeric($catID) == false)
				$field = "slug";

			//check for special chars

			$lastChar = substr($catID, -1);
			switch($lastChar){
				case "*":    //force include children
					$isIncludeChildren = true;
					$catID = substr($catID, 0, -1);    //remove last char
				break;
			}

			$arrSearchItem = array();
			$arrSearchItem["taxonomy"] = $taxName;
			$arrSearchItem["field"] = $field;
			$arrSearchItem["terms"] = $catID;
			$arrSearchItem["include_children"] = $isIncludeChildren;

			if($isExclude == true){
				$arrSearchItem["operator"] = "NOT IN";
			}

			$arrQuery[] = $arrSearchItem;
		}

		return ($arrQuery);
	}

	/**
	 * group tax query by taxonomies
	 */
	public static function groupTaxQuery($arrQuery){
		
		if(empty($arrQuery))
			return($arrQuery);
			
		$arrTermsByTax = array();
		
		foreach($arrQuery as $term){
			
			$taxonomy = UniteFunctionsUC::getVal($term, "taxonomy");
			
			$arrTerms = UniteFunctionsUC::getVal($arrTermsByTax, $taxonomy);
			if(empty($arrTerms))
				$arrTerms = array();
			
			$arrTerms[] = $term;
			
			$arrTermsByTax[$taxonomy] = $arrTerms;
			
		}
		
		if(count($arrTermsByTax) == 1)
			return($arrQuery);
			
		//combine new query

		$arrQueryNew = array();
		
		foreach($arrTermsByTax as $taxonomy => $arrGroup){
			
			$numTerms = count($arrGroup);
			
			//add single term or term group
			
			if($numTerms == 1)
				$arrQueryNew[] = $arrGroup[0];
			else{
				$arrGroup["relation"] = "OR";
				$arrQueryNew[] = $arrGroup;
			}
		}
		
		
		return($arrQueryNew);
	}
	
	
	/**
	 * get taxanomy query
	 * $categoryRelation - null, OR, GROUP
	 */
	public static function getPosts_getTaxQuery($category, $categoryRelation = null, $isIncludeChildren = false, $excludeCategory = null, $isExcludeChildren = true){

				
		if(empty($category) && empty($excludeCategory))
			return (null);

		if($category == "all" && empty($excludeCategory))
			return (null);
		
		//get the query
		$arrQuery = array();
		$arrQueryExclude = array();

		if(!empty($category))
			$arrQuery = self::getPosts_getTaxQuery_getArrQuery($arrQuery, $category, $categoryRelation, $isIncludeChildren, false);

		$numQueryItems = count($arrQuery);

		if(!empty($excludeCategory))
			$arrQueryExclude = self::getPosts_getTaxQuery_getArrQuery($arrQueryExclude, $excludeCategory, $categoryRelation, $isExcludeChildren, true);
		
		//make nested - if both filled
		if(!empty($arrQueryExclude) && !empty($arrQuery) && $numQueryItems > 1 && $categoryRelation === "OR"){
			//check and add relation
			$arrQuery["relation"] = "OR";

			$arrQueryCombined = array();
			$arrQueryCombined[] = $arrQuery;
			$arrQueryCombined[] = $arrQueryExclude;

			return ($arrQueryCombined);
		}

		//in case there is exclude only
		if(!empty($arrQueryExclude))
			$arrQuery = array_merge($arrQuery, $arrQueryExclude);

		//for single query
		if(empty($arrQuery))
			return (null);

		if(count($arrQuery) == 1)
			return ($arrQuery);
		
		if($categoryRelation == "GROUP"){
			$arrQuery = self::groupTaxQuery($arrQuery);
			return($arrQuery);
		}
		
		//check and add relation
		if($categoryRelation === "OR" && $numQueryItems > 1){
			$arrQuery = array($arrQuery);

			$arrQuery[0]["relation"] = "OR";
		}

		return ($arrQuery);
	}

	/**
	 * update order by
	 */
	public static function updatePostArgsOrderBy($args, $orderBy){

		$arrOrderKeys = self::getArrSortBy();

		if(isset($arrOrderKeys[$orderBy])){
			$args["orderby"] = $orderBy;
			
			return ($args);
		}

		switch($orderBy){
			case "price":
				$args["orderby"] = "meta_value_num";
				$args["meta_key"] = "_price";
			break;
		}

		return ($args);
	}

	/**
	 * get posts arguments by filters
	 * filters: search, category, category_relation, posttype, orderby, limit
	 */
	public static function getPostsArgs($filters, $isTaxonly = false){

		$args = array();

		$category = UniteFunctionsUC::getVal($filters, "category");
		$categoryRelation = UniteFunctionsUC::getVal($filters, "category_relation");
		$categoryIncludeChildren = UniteFunctionsUC::getVal($filters, "category_include_children");
		
		$excludeCategory = UniteFunctionsUC::getVal($filters, "exclude_category");

		$categoryExcludeChildren = UniteFunctionsUC::getVal($filters, "category_exclude_children");
		$categoryExcludeChildren = UniteFunctionsUC::strToBool($categoryExcludeChildren);

		$arrTax = self::getPosts_getTaxQuery($category, $categoryRelation, $categoryIncludeChildren, $excludeCategory, $categoryExcludeChildren);
		
		if($isTaxonly === true){
			if(!empty($arrTax)){
				if(count($arrTax) > 1){
					$arrTax = array($arrTax);
				}

				$args["tax_query"] = $arrTax;
			}

			return ($args);
		}

		$search = UniteFunctionsUC::getVal($filters, "search");
		if(!empty($search))
			$args["s"] = $search;

		$postType = UniteFunctionsUC::getVal($filters, "posttype");

		if(is_array($postType) && count($postType) == 1)
			$postType = $postType[0];

		$args["post_type"] = $postType;

		if(!empty($arrTax))
			$args["tax_query"] = $arrTax;

		//process orderby
		$orderby = UniteFunctionsUC::getVal($filters, "orderby");

		if(!empty($orderby))
			$args["orderby"] = $orderby;

		if($orderby == self::SORTBY_META_VALUE || $orderby == self::SORTBY_META_VALUE_NUM)
			$args["meta_key"] = UniteFunctionsUC::getVal($filters, "meta_key");

		$isProduct = ($postType == "product");

		//order product by price
		if($isProduct && $orderby == self::SORTBY_PRICE){
			$args["orderby"] = "meta_value_num";
			$args["meta_key"] = "_price";
		}

		if($isProduct && $orderby == self::SORTBY_SALE_PRICE){
			$args["orderby"] = "meta_value_num";
			$args["meta_key"] = "_sale_price";
		}

		$orderDir = UniteFunctionsUC::getVal($filters, "orderdir");

		if(!empty($orderDir))
			$args["order"] = $orderDir;

		$args["posts_per_page"] = UniteFunctionsUC::getVal($filters, "limit");

		$postStatus = UniteFunctionsUC::getVal($filters, "status");
		if(!empty($postStatus))
			$args["post_status"] = $postStatus;

		//get exlude posts
		$excludeCurrentPost = UniteFunctionsUC::getVal($filters, "exclude_current_post");
		$excludeCurrentPost = UniteFunctionsUC::strToBool($excludeCurrentPost);

		if($excludeCurrentPost == true){
			$postID = get_the_ID();
			if(!empty($postID)){
				$args["post__not_in"] = array($postID);
			}
		}

		return ($args);
	}

	/**
	 * get posts post type
	 */
	public static function getPosts($filters){

		$args = self::getPostsArgs($filters);

		$arrPosts = get_posts($args);

		if(empty($arrPosts))
			$arrPosts = array();

		return ($arrPosts);
	}

	/**
	 * order posts by id's
	 */
	public static function orderPostsByIDs($arrPosts, $arrPostIDs){

		if(empty($arrPostIDs))
			return ($arrPosts);

		$arrPostsAssoc = array();
		foreach($arrPosts as $post){
			$arrPostsAssoc[$post->ID] = $post;
		}

		$arrOutput = array();
		foreach($arrPostIDs as $postID){
			$post = UniteFunctionsUC::getVal($arrPostsAssoc, $postID);
			if(empty($post))
				continue;

			$arrOutput[] = $post;
		}

		return ($arrOutput);
	}

	/**
	 * get page template
	 */
	public static function getPostPageTemplate($post){

		if(empty($post))
			return ("");

		$arrPost = $post->to_array();
		$pageTemplate = UniteFunctionsUC::getVal($arrPost, "page_template");

		return ($pageTemplate);
	}

	/**
	 * get edit post url
	 */
	public static function getUrlEditPost($postID, $encodeForJS = false){

		$context = "display";
		if($encodeForJS == false)
			$context = "normal";

		$urlEditPost = get_edit_post_link($postID, $context);

		return ($urlEditPost);
	}

	/**
	 * check if current user can edit post
	 */
	public static function isUserCanEditPost($postID){

		$post = get_post($postID);

		if(empty($post))
			return (false);

		$postStatus = $post->post_status;
		if($postStatus == "trash")
			return (false);

		$postType = $post->post_type;

		$objPostType = get_post_type_object($postType);
		if(empty($objPostType))
			return (false);

		if(isset($objPostType->cap->edit_post) == false){
			return false;
		}

		$editCap = $objPostType->cap->edit_post;

		$isCanEdit = current_user_can($editCap, $postID);
		if($isCanEdit == false)
			return (false);

		$postsPageID = get_option('page_for_posts');
		if($postsPageID === $postID)
			return (false);

		return (true);
	}

	/**
	 * get post titles by ids
	 */
	public static function getPostTitlesByIDs($arrIDs){

		$db = self::getDB();

		$tablePosts = UniteProviderFunctionsUC::$tablePosts;

		$strIDs = implode(",", $arrIDs);

		if(empty($strIDs))
			return (array());

		$strIDs = $db->escape($strIDs);

		$sql = "select ID as id,post_title as title, post_type as type from $tablePosts where ID in($strIDs)";

		$response = $db->fetchSql($sql);

		if(empty($response))
			return (array());

		//--- keep original order

		$response = UniteFunctionsUC::arrayToAssoc($response, "id");

		$output = array();
		foreach($arrIDs as $id){
			$item = UniteFunctionsUC::getVal($response, $id);
			if(empty($item))
				continue;

			$output[] = $item;
		}

		return ($output);
	}

	/**
	 * get post content
	 */
	public static function getPostContent($post){

		if(empty($post))
			return ("");

		//UniteFunctionsUC::showTrace();
		//dmp($post);

		$postID = $post->ID;

		//protection against infinate loops

		if(isset(self::$cachePostContent[$postID]))
			return (self::$cachePostContent[$postID]);

		self::$cachePostContent[$postID] = $post->post_content;

		$isEditMode = GlobalsProviderUC::$isInsideEditor;

		if($isEditMode == false)
			$content = get_the_content(null, false, $post);
		else
			$content = $post->post_content;

		if(GlobalsProviderUC::$disablePostContentFiltering !== true)
			$content = apply_filters("widget_text_content", $content);

		self::$cachePostContent[$postID] = $content;

		return ($content);
	}

	/**
	 * get next or previous post
	 */
	public static function getNextPrevPostData($type = "next", $taxonomy = "category"){

		if(empty($taxonomy))
			$taxonomy = "category";

		if(empty($type))
			$type = "next";

		$previous = !($type == "next");

		if($previous && is_attachment()){
			$post = get_post(get_post()->post_parent);
		}else{
			$in_same_term = false;
			$excluded_terms = '';

			$post = get_adjacent_post($in_same_term, $excluded_terms, $previous, $taxonomy);
		}

		if(empty($post))
			return (null);

		$title = $post->post_title;

		$link = get_permalink($post);

		$output = array();

		$output["title"] = $title;
		$output["link"] = $link;

		return ($output);
	}

	public static function a__________POST_ACTIONS_________(){
	}

	/**
	 * update post type
	 */
	public static function updatePost($postID, $arrUpdate){

		if(empty($arrUpdate))
			UniteFunctionsUC::throwError("nothing to update post");

		$arrUpdate["ID"] = $postID;

		$wpError = wp_update_post($arrUpdate, true);

		if(is_wp_error($wpError)){
			UniteFunctionsUC::throwError("Error updating post: $postID");
		}
	}

	/**
	 * add prefix to post permalink
	 */
	public static function addPrefixToPostName($postID, $prefix){

		$post = get_post($postID);
		if(empty($post))
			return (false);

		$postName = $post->post_name;

		//check if already exists
		if(strpos($postName, $prefix) === 0)
			return (false);

		$newPostName = $prefix . $postName;

		$arrUpdate = array();
		$arrUpdate["post_name"] = $newPostName;

		self::updatePost($postID, $arrUpdate);

		$post = get_post($postID);
	}

	/**
	 * update post ordering
	 */
	public static function updatePostOrdering($postID, $ordering){

		$arrUpdate = array(
			'menu_order' => $ordering,
		);

		self::updatePost($postID, $arrUpdate);
	}

	/**
	 * update post content
	 */
	public static function updatePostContent($postID, $content){

		$arrUpdate = array("post_content" => $content);
		self::updatePost($postID, $arrUpdate);
	}

	/**
	 * update post page template attribute in meta
	 */
	public static function updatePageTemplateAttribute($pageID, $pageTemplate){

		update_post_meta($pageID, "_wp_page_template", $pageTemplate);
	}

	/**
	 * insert post
	 * params: [cat_slug, content]
	 */
	public static function insertPost($title, $alias, $params = array()){

		$catSlug = UniteFunctionsUC::getVal($params, "cat_slug");
		$content = UniteFunctionsUC::getVal($params, "content");
		$isPage = UniteFunctionsUC::getVal($params, "ispage");
		$isPage = UniteFunctionsUC::strToBool($isPage);

		$catID = null;
		if(!empty($catSlug)){
			$catID = self::getCatIDBySlug($catSlug);
			if(empty($catID))
				UniteFunctionsUC::throwError("Category id not found by slug: $catSlug");
		}

		$isPostExists = self::isPostNameExists($alias);

		if($isPostExists == true)
			UniteFunctionsUC::throwError("Post with name: <b> {$alias} </b> already exists");

		$arguments = array();
		$arguments["post_title"] = $title;
		$arguments["post_name"] = $alias;
		$arguments["post_status"] = "publish";

		if(!empty($content))
			$arguments["post_content"] = $content;

		if(!empty($catID))
			$arguments["post_category"] = array($catID);

		if($isPage == true)
			$arguments["post_type"] = "page";

		$postType = UniteFunctionsUC::getVal($params, "post_type");
		if(!empty($postType))
			$arguments["post_type"] = $postType;

		$newPostID = wp_insert_post($arguments, true);

		if(is_wp_error($newPostID)){
			$errorMessage = $newPostID->get_error_message();
			UniteFunctionsUC::throwError($errorMessage);
		}

		return ($newPostID);
	}

	/**
	 * insert new page
	 */
	public static function insertPage($title, $alias, $params = array()){

		$params["ispage"] = true;

		$pageID = self::insertPost($title, $alias, $params);

		return ($pageID);
	}

	/**
	 * delete all post metadata
	 */
	public static function deletePostMetadata($postID){

		$postID = (int)$postID;

		$tablePostMeta = UniteProviderFunctionsUC::$tablePostMeta;

		$db = self::getDB();
		$db->delete($tablePostMeta, "post_id=$postID");
	}

	/**
	 * delete multiple posts
	 */
	public static function deleteMultiplePosts($arrPostIDs){

		if(empty($arrPostIDs))
			return (false);

		if(is_array($arrPostIDs) == false)
			return (false);

		foreach($arrPostIDs as $postID){
			self::deletePost($postID);
		}
	}

	/**
	 * delete post
	 */
	public static function deletePost($postID){

		wp_delete_post($postID, true);
	}

	/**
	 * cache attachment images query calls. one call instead of many
	 * input - post array.
	 */
	public static function cachePostsAttachmentsQueries($arrPosts){

		if(empty($arrPosts))
			return (false);

		$arrAttachmentIDs = self::getPostsAttachmentsIDs($arrPosts);

		if(empty($arrAttachmentIDs))
			return (false);

		self::cachePostMetaQueries($arrAttachmentIDs);
	}

	/**
	 * cache post meta queries by id's
	 */
	public static function cachePostMetaQueries($arrPostIDs){

		if(empty($arrPostIDs))
			return (false);

		_prime_post_caches($arrPostIDs);
	}

	/**
	 * get post terms queries
	 */
	public static function cachePostsTermsQueries($arrPosts){

		if(empty($arrPosts))
			return (false);

		$arrIDs = array();

		//single type for now
		$postType = null;

		foreach($arrPosts as $post){
			if(empty($postType))
				$postType = $post->post_type;

			$arrIDs[] = $post->ID;
		}

		$arrTaxonomies = self::getPostTypeTaxomonies($postType);

		if(empty($arrTaxonomies))
			return (false);

		$arrTaxKeys = array_keys($arrTaxonomies);

		//get all terms

		$args = array();
		$args["fields"] = "all_with_object_id";

		$arrTerms = wp_get_object_terms($arrIDs, $arrTaxKeys, $args);

		$arrTermsByPosts = array();

		foreach($arrTerms as $term){
			$postID = $term->object_id;

			if(isset(GlobalsProviderUC::$arrPostTermsCache[$postID]) == false)
				$arrTermsByPosts[$postID] = array();

			$taxonomy = $term->taxonomy;

			$termID = $term->term_id;

			GlobalsProviderUC::$arrPostTermsCache[$postID][$taxonomy][$termID] = $term;
		}
	}

	public static function a__________ATTACHMENT________(){
	}

	/**
	 * get attachmet id's from post
	 */
	public static function getPostsAttachmentsIDs($arrPosts){

		if(empty($arrPosts))
			return (false);

		$arrIDs = array();

		foreach($arrPosts as $post){
			$postID = $post->ID;

			$featuredImageID = self::getFeaturedImageID($postID);

			if(empty($featuredImageID))
				continue;

			$arrIDs[] = $featuredImageID;
		}

		return ($arrIDs);
	}

	/**
	 * get first image id from content
	 */
	public static function getFirstImageIDFromContent($content){

		$strSearch = "class=\"wp-image-";

		$posImageClass = strpos($content, $strSearch);

		if($posImageClass === false)
			return (null);

		$posSearch2 = $posImageClass + strlen($strSearch);

		$posIDEnd = strpos($content, "\"", $posSearch2);

		if($posIDEnd === false)
			return (null);

		$imageID = substr($content, $posSearch2, $posIDEnd - $posSearch2);

		$imageID = (int)$imageID;

		return ($imageID);
	}

	/**
	 * get post thumb id from post id
	 */
	public static function getFeaturedImageID($postID){

		$thumbID = get_post_thumbnail_id($postID);

		return ($thumbID);
	}

	/**
	 *
	 * get attachment image url
	 */
	public static function getUrlAttachmentImage($thumbID, $size = self::THUMB_FULL){

		$handle = "thumb_{$thumbID}_{$size}";

		if(isset(self::$arrUrlThumbCache[$handle]))
			return (self::$arrUrlThumbCache[$handle]);

		//wpml integration - get translated media id for current language

		$isWPML = UniteCreatorWpmlIntegrate::isWpmlExists();

		if($isWPML)
			$thumbID = UniteCreatorWpmlIntegrate::getTranslatedAttachmentID($thumbID);

		$arrImage = wp_get_attachment_image_src($thumbID, $size);
		if(empty($arrImage))
			return (false);

		$url = UniteFunctionsUC::getVal($arrImage, 0);

		self::$arrUrlThumbCache[$handle] = $url;

		return ($url);
	}

	/**
	 * get image data by url
	 */
	public static function getImageDataByUrl($urlImage){

		$title = HelperUC::getTitleFromUrl($urlImage, "image");

		$item = array();
		$item["image_id"] = "";
		$item["image"] = $urlImage;
		$item["thumb"] = $urlImage;
		$item["title"] = $title;
		$item["description"] = "";

		return ($item);
	}

	/**
	 * get product category image
	 */
	public static function getProductCatImage($productCatID){

		$imageID = get_term_meta($productCatID, "thumbnail_id", true);

		if(empty($imageID))
			return ("");

		$urlImage = self::getUrlAttachmentImage($imageID, UniteFunctionsWPUC::THUMB_LARGE);

		return ($urlImage);
	}

	/**
	 * get attachment data
	 */
	public static function getAttachmentData($thumbID){

		//try to return data by url
		/*
			if(is_numeric($thumbID) == false){

				$urlImage = $thumbID;
				$thumbID = self::getAttachmentIDFromImageUrl($thumbID);

				if(empty($thumbID)){

					$imageData = self::getImageDataByUrl($urlImage);

					return($imageData);
				}
			}
			*/

		if(empty($thumbID))
			return (null);

		if(is_numeric($thumbID) == false){
			$imageData = self::getImageDataByUrl($thumbID);

			return ($imageData);
		}

		$handle = "attachment_data_$thumbID";
		if(isset(self::$arrUrlAttachmentDataCache[$handle]))
			return (self::$arrUrlAttachmentDataCache[$handle]);

		$post = get_post($thumbID);
		if(empty($post))
			return (null);

		$title = wp_get_attachment_caption($thumbID);

		$rawCaption = $title;

		$item = array();
		$item["image_id"] = $post->ID;
		$item["image"] = $post->guid;

		if(empty($title))
			$title = $post->post_title;

		$rawTitle = $post->post_title;

		$urlThumb = self::getUrlAttachmentImage($thumbID, self::THUMB_MEDIUM_LARGE);
		if(empty($urlThumb))
			$urlThumb = $post->guid;

		$urlThumbLarge = self::getUrlAttachmentImage($thumbID, self::THUMB_LARGE);
		if(empty($urlThumbLarge))
			$urlThumbLarge = $urlThumb;

		$item["thumb"] = $urlThumb;
		$item["thumb_large"] = $urlThumb;

		$item["title"] = $title;
		$item["description"] = $post->post_content;

		$item["raw_caption"] = $rawCaption;
		$item["raw_title"] = $rawTitle;

		self::$arrUrlAttachmentDataCache[$handle] = $item;

		return ($item);
	}

	/**
	 * get thumbnail sizes array
	 */
	public static function getArrThumbSizes(){

		if(!empty(self::$arrThumbSizesCache))
			return (self::$arrThumbSizesCache);

		global $_wp_additional_image_sizes;

		$arrWPSizes = get_intermediate_image_sizes();

		$arrSizes = array();

		foreach($arrWPSizes as $size){
			$title = UniteFunctionsUC::convertHandleToTitle($size);

			$maxWidth = null;
			$maxHeight = null;
			$isCrop = false;

			//get max width from option or additional sizes array
			$arrSize = UniteFunctionsUC::getVal($_wp_additional_image_sizes, $size);
			if(!empty($arrSize)){
				$maxWidth = UniteFunctionsUC::getVal($arrSize, "width");
				$maxHeight = UniteFunctionsUC::getVal($arrSize, "height");
				$crop = UniteFunctionsUC::getVal($arrSize, "crop");
			}

			if(empty($maxWidth)){
				$maxWidth = intval(get_option("{$size}_size_w"));
				$maxHeight = intval(get_option("{$size}_size_h"));
				$crop = intval(get_option("{$size}_crop"));
			}

			if(empty($maxWidth)){
				$arrSizes[$size] = $title;
				continue;
			}

			//add the text addition
			$addition = "";
			if($crop == true)
				$addition = "({$maxWidth}x{$maxHeight})";
			else
				$addition = "(max width $maxWidth)";

			$title .= " " . $addition;

			$arrSizes[$size] = $title;
		}

		$arrSizes["full"] = __("Full Size", "unlimited-elements-for-elementor");

		//sort
		$arrNew = array();

		$topKeys = array("medium_large", "large", "medium", "thumbnail", "full");

		foreach($topKeys as $key){
			if(!isset($arrSizes[$key]))
				continue;

			$arrNew[$key] = $arrSizes[$key];
			unset($arrSizes[$key]);
		}

		$arrNew = array_merge($arrNew, $arrSizes);

		self::$arrThumbSizesCache = $arrNew;

		return ($arrNew);
	}

	/**
	 * Get an attachment ID given a URL.
	 *
	 * @param string $url
	 *
	 * @return int Attachment ID on success, 0 on failure
	 */
	public static function getAttachmentIDFromImageUrl($url){

		if(empty($url))
			return (null);

		$attachment_id = 0;

		$dir = wp_upload_dir();

		if(false !== strpos($url, $dir['baseurl'] . '/')){ // Is URL in uploads directory?

			$file = basename($url);

			$query_args = array(
				'post_type' => 'attachment',
				'post_status' => 'inherit',
				'fields' => 'ids',
				'meta_query' => array(
					array(
						'value' => $file,
						'compare' => 'LIKE',
						'key' => '_wp_attachment_metadata',
					),
				),
			);

			$query = new WP_Query($query_args);

			if($query->have_posts()){
				foreach($query->posts as $post_id){
					$meta = wp_get_attachment_metadata($post_id);

					$original_file = basename($meta['file']);
					$cropped_image_files = wp_list_pluck($meta['sizes'], 'file');

					if($original_file === $file || in_array($file, $cropped_image_files)){
						$attachment_id = $post_id;
						break;
					}
				}
			}
		}

		return $attachment_id;
	}

	/**
	 * get attachment post title
	 */
	public static function getAttachmentPostTitle($post){

		if(empty($post))
			return ("");

		$post = (array)$post;

		$title = UniteFunctionsUC::getVal($post, "post_title");
		$filename = UniteFunctionsUC::getVal($post, "guid");

		if(empty($title))
			$title = $filename;

		$info = pathinfo($title);
		$name = UniteFunctionsUC::getVal($info, "filename");

		if(!empty($name))
			$title = $name;

		return ($title);
	}

	/**
	 * get attachment post alt
	 */
	public static function getAttachmentPostAlt($postID){

		$alt = get_post_meta($postID, '_wp_attachment_image_alt', true);

		return ($alt);
	}

	public static function a___________USER_DATA__________(){
	}

	/**
	 *
	 * validate permission that the user is admin, and can manage options.
	 */
	public static function isAdminPermissions(){

		if(is_admin() && current_user_can("manage_options"))
			return (true);

		return (false);
	}

	/**
	 * check if current user has some permissions
	 */
	public static function isCurrentUserHasPermissions(){

		$canEdit = current_user_can("manage_options");

		return ($canEdit);
	}

	/**
	 * get keys of user meta
	 */
	public static function getUserMetaKeys(){

		$arrKeys = array(
			"first_name",
			"last_name",
			"description",

			"billing_first_name",
			"billing_last_name",
			"billing_company",
			"billing_address_1",
			"billing_address_2",
			"billing_city",
			"billing_postcode",
			"billing_country",
			"billing_state",
			"billing_phone",
			"billing_email",
			"billing_first_name",
			"billing_last_name",

			"shipping_company",
			"shipping_address_1",
			"shipping_address_2",
			"shipping_city",
			"shipping_postcode",
			"shipping_country",
			"shipping_state",
			"shipping_phone",
			"shipping_email",
		);

		return ($arrKeys);
	}

	/**
	 * get user avatar keys
	 */
	public static function getUserAvatarKeys(){

		$arrKeys = array(
			"avatar_found",
			"avatar_url",
			"avatar_size",
		);

		return ($arrKeys);
	}

	/**
	 * get user meta
	 */
	public static function getUserMeta($userID, $arrMetaKeys = null, $addPrefixed = false){

		$arrMeta = get_user_meta($userID, '', true);

		if(empty($arrMeta))
			return (null);

		$arrKeys = self::getUserMetaKeys();

		if(is_array($arrMetaKeys) == false)
			$arrMetaKeys = array();

		if(!empty($arrMetaKeys))
			$arrKeys = array_merge($arrKeys, $arrMetaKeys);

		$arrMetaKeys = UniteFunctionsUC::arrayToAssoc($arrMetaKeys);

		$arrOutput = array();
		foreach($arrKeys as $key){
			$metaValue = UniteFunctionsUC::getVal($arrMeta, $key);

			if(is_array($metaValue))
				$metaValue = $metaValue[0];

			//from the additional - try to unserialize
			if(isset($arrMetaKeys[$key]) && is_string($metaValue)){
				$arrOpened = maybe_unserialize($metaValue);
				if(!empty($arrOpened))
					$metaValue = $arrOpened;
			}

			if($addPrefixed == true)
				$key = "cf_" . $key;

			$arrOutput[$key] = $metaValue;
		}

		return ($arrOutput);
	}

	/**
	 * get user avatar data
	 */
	public static function getUserAvatarData($userID, $urlDefaultImage = ""){

		$args = array();

		if(!empty($urlDefaultImage))
			$args["default"] = $urlDefaultImage;

		$arrAvatar = get_avatar_data($userID, $args);

		$hasAvatar = UniteFunctionsUC::getVal($arrAvatar, "found_avatar");
		$size = UniteFunctionsUC::getVal($arrAvatar, "size");
		$url = UniteFunctionsUC::getVal($arrAvatar, "url");

		$arrOutput = array();
		$arrOutput["avatar_found"] = $hasAvatar;
		$arrOutput["avatar_url"] = $url;
		$arrOutput["avatar_size"] = $size;

		return ($arrOutput);
	}

	/**
	 * get user data by object
	 */
	public static function getUserData($objUser, $getMeta = false, $getAvatar = false, $arrMetaKeys = null){

		if(is_numeric($objUser))
			$objUser = get_user_by("id", $objUser);

		$userID = $objUser->ID;

		$urlPosts = get_author_posts_url($userID);

		if($getMeta == true)
			$numPosts = count_user_posts($userID);

		$userData = $objUser->data;

		$userData = UniteFunctionsUC::convertStdClassToArray($userData);

		$arrData = array();
		$arrData["id"] = UniteFunctionsUC::getVal($userData, "ID");

		$username = UniteFunctionsUC::getVal($userData, "user_nicename");

		$arrData["username"] = $username;

		$name = UniteFunctionsUC::getVal($userData, "display_name");
		if(empty($name))
			$name = $username;

		if(empty($name))
			$name = UniteFunctionsUC::getVal($userData, "user_login");

		$arrData["name"] = $name;

		$arrData["user_login"] = UniteFunctionsUC::getVal($userData, "user_login");

		$arrData["email"] = UniteFunctionsUC::getVal($userData, "user_email");

		$arrData["url_posts"] = $urlPosts;

		if($getMeta == true)
			$arrData["num_posts"] = $numPosts;

		if($getAvatar == true){
			$arrAvatar = self::getUserAvatarData($userID);
			if(!empty($arrAvatar))
				$arrData = $arrData + $arrAvatar;
		}

		//add role
		$arrRoles = $objUser->roles;

		$role = "";
		if(!empty($arrRoles))
			$role = implode(",", $arrRoles);

		$arrData["role"] = $role;

		$urlWebsite = UniteFunctionsUC::getVal($userData, "user_url");
		$arrData["website"] = $urlWebsite;

		//add meta
		if($getMeta == true){
			$arrMeta = self::getUserMeta($userID, $arrMetaKeys);
			if(!empty($arrMeta))
				$arrData = $arrData + $arrMeta;
		}

		return ($arrData);
	}

	/**
	 * get user data by id
	 * if user not found, return empty data
	 */
	public static function getUserDataById($userID, $getMeta = false, $getAvatar = false){

		if($userID == "loggedin_user")
			$objUser = wp_get_current_user();
		else{
			if(is_numeric($userID))
				$objUser = get_user_by("id", $userID);
			else
				$objUser = get_user_by("slug", $userID);
		}

		//if emtpy user - return empty
		if(empty($objUser)){
			$arrEmpty = array();
			$arrEmpty["id"] = "";
			$arrEmpty["name"] = "";
			$arrEmpty["email"] = "";

			return ($arrEmpty);
		}

		$arrData = self::getUserData($objUser, $getMeta, $getAvatar);

		return ($arrData);
	}

	/**
	 * get roles as name/value array
	 */
	public static function getRolesShort($addAll = false){

		$objRoles = wp_roles();

		$arrShort = $objRoles->role_names;

		if($addAll == true){
			$arrAll["__all__"] = __("[All Roles]", "unlimited-elements-for-elementor");
			$arrShort = $arrAll + $arrShort;
		}

		return ($arrShort);
	}

	/**
	 * get menus list short - id / title
	 */
	public static function getMenusListShort(){

		$arrShort = array();

		$arrMenus = get_terms("nav_menu");

		if(empty($arrMenus))
			return (array());

		foreach($arrMenus as $menu){
			$menuID = $menu->term_id;
			$name = $menu->name;

			$arrShort[$menuID] = $name;
		}

		return ($arrShort);
	}

	/**
	 * get users array short
	 */
	public static function getArrAuthorsShort($addCurrentUser = false){

		if(!empty(self::$cacheAuthorsShort)){
			if($addCurrentUser){
				$arrUsers = UniteFunctionsUC::addArrFirstValue(self::$cacheAuthorsShort, "-- Logged In User --", "uc_loggedin_user");

				return ($arrUsers);
			}

			return (self::$cacheAuthorsShort);
		}

		$args = array("role__not_in" => array("subscriber", "customer"));
		$arrUsers = get_users($args);

		$arrUsersShort = array();

		$arrNames = array();
		$arrAlternative = array();

		foreach($arrUsers as $objUser){
			$userID = $objUser->ID;
			$userData = $objUser->data;
			$name = $userData->display_name;
			if(empty($name))
				$name = $userData->user_nicename;
			if(empty($name))
				$name = $userData->user_login;

			$login = $userData->user_login;
			$alternativeName = $name . " ({$login})";

			//avoid duplicate names

			if(isset($arrNames[$name])){
				$oridinalUserID = $arrNames[$name];

				$arrUsersShort[$oridinalUserID] = $arrAlternative[$name];

				$name = $alternativeName;
			}else{
				$arrAlternative[$name] = $alternativeName;
			}

			$arrNames[$name] = $userID;

			$arrUsersShort[$userID] = $name;
		}

		self::$cacheAuthorsShort = $arrUsersShort;

		if($addCurrentUser == true){
			$arrUsers = UniteFunctionsUC::addArrFirstValue(self::$cacheAuthorsShort, "-- Logged In User --", "uc_loggedin_user");

			return ($arrUsers);
		}

		return ($arrUsersShort);
	}

	public static function a___________MENU__________(){
	}

	/**
	 * get menu items
	 */
	public static function getMenuItems($menuID, $isOnlyParents = false){

		$objMenu = wp_get_nav_menu_object($menuID);

		if(empty($objMenu))
			return (array());

		$arrItems = wp_get_nav_menu_items($objMenu);

		if(empty($arrItems))
			return (array());

		$arrItemsData = array();

		foreach($arrItems as $objItem){
			$item = array();

			$parentID = $objItem->menu_item_parent;

			if($isOnlyParents == true && !empty($parentID)){
				continue;
			}

			$url = $objItem->url;
			$title = $objItem->title;
			$titleAttribute = $objItem->attr_title;
			$target = $objItem->target;

			$item["id"] = $objItem->ID;
			$item["type"] = $objItem->type_label;
			$item["title"] = $objItem->title;
			$item["url"] = $objItem->url;
			$item["target"] = $objItem->target;
			$item["title_attribute"] = $objItem->attr_title;
			$item["description"] = $objItem->description;

			$arrClasses = $objItem->classes;

			$strClases = "";
			if(!empty($arrClasses))
				$strClases = implode(" ", $arrClasses);

			$strClases = trim($strClases);

			$item["classes"] = $strClases;

			//make the html

			$addHtml = "";
			if(!empty($target))
				$addHtml .= " target='$target'";

			if(!empty($titleAttribute)){
				$titleAttribute = esc_attr($titleAttribute);
				$addHtml .= " title='$titleAttribute'";
			}

			if(!empty($strClases))
				$addHtml .= " class='$strClases'";

			$html = "<a href='{$url}' {$addHtml}>{$title}</a>";

			$item["html_link"] = $html;

			$arrItemsData[] = $item;
		}

		return ($arrItemsData);
	}

	private static function a_________________QUERY_VARS_____________(){}
	
	
	/**
	 * get current query vars
	 */
	public static function getCurrentQueryVars(){
		
		global $wp_query;
		
		if(empty($wp_query))
			return(array());
		
		$currentQueryVars = $wp_query->query_vars;
		
		return($currentQueryVars);
	}
	
	
	/**
	 * clean query args for debug
	 */
	public static function cleanQueryArgsForDebug($args){

		$argsNew = array();

		foreach($args as $name => $value){
			//keep
			switch($name){
				case "ignore_sticky_posts":
				case "suppress_filters":

					$argsNew[$name] = $value;
					continue(2);
				break;
			}

			if(empty($value))
				continue;

			$argsNew[$name] = $value;
		}

		return ($argsNew);
	}

	/**
	 * print current query
	 */
	public static function printCurrentQuery($query = null){

		if(empty($query)){
			global $wp_query;
			$query = $wp_query;
		}

		$queryVars = $query->query_vars;

		$queryVars = self::cleanQueryArgsForDebug($queryVars);

		dmp("Current Query Is: ");
		dmp($queryVars);
	}
	
	/**
	 * merge the query vars
	 */
	public static function mergeQueryVars($args1, $args2){
		
		//merge the arrays
		$args = array();
		
		foreach($args1 as $key=>$value){
			
			if(is_array($value) == false)
				continue;
				
			if(empty($value))
				continue;
			
			$arrValue2 = UniteFunctionsUC::getVal($args2, $key);
			
			if(is_array($arrValue2) == false)
				continue;
				
			if(empty($arrValue2))
				continue;
			
			if($key == "tax_query"){
				
				$args1 = self::mergeArgsTaxQuery($args1, $arrValue2);
				
				unset($args2[$key]);
								
				continue;
			}
			
			$value = array_merge($value, $arrValue2);

			$args1[$key] = $value;
			
			unset($args2[$key]);
			
		}
		
		$args = array_merge($args1,$args2);
		
		
		return($args);
	}
	
	public static function a___________OTHER_FUNCTIONS__________(){
	}

	/**
	 * find and remove some include
	 */
	public static function findAndRemoveInclude($filename, $isJS = true){


		if($isJS == false)
			$objScripts = wp_styles();
		else
			$objScripts = wp_scripts();


		if(empty($objScripts))
			return(false);

		$arrDeleted = array();

		foreach( $objScripts->queue as $scriptName ){
		
			$objScript = UniteFunctionsUC::getVal($objScripts->registered, $scriptName);

			if(empty($objScript))
				continue;
						
			$url = $objScript->src;
			
			//find by handle
			$isFound = false;
				
			if($scriptName == $filename)
				$isFound = true;
			else{
			
				//find by url
	
				$url = strtolower($url);
				
				$isFound = strpos($url, $filename);
			}
			
			if($isFound === false)
				continue;

			$arrDeleted[] = $url;

			if($isJS == true)
				wp_dequeue_script( $scriptName );
			else
				wp_dequeue_style( $scriptName );

		}

		return($arrDeleted);
	}


	/**
	 * set global author data variable
	 */
	public static function setGlobalAuthorData($post = null) {

		global $authordata;

		if(empty($post))
			$post = get_post();

		$authordata = get_userdata( $post->post_author );

	}

	/**
	 * get wordpress language
	 */
	public static function getLanguage(){

		$locale = get_locale();
		if(is_string($locale) == false)
			return ("en");

		$pos = strpos($locale, "_");

		if($pos === false)
			return ($locale);

		$lang = substr($locale, 0, $pos);

		return ($lang);
	}

	/**
	 * get install plugin slug
	 */
	public static function getInstallPluginLink($slug){

		$action = 'install-plugin';

		$urlInstall = wp_nonce_url(
			add_query_arg(
				array(
					'action' => $action,
					'plugin' => $slug,
				),
				admin_url('update.php')
			),
			$action . '_' . $slug
		);

		return ($urlInstall);
	}

	/**
	 * get queried object by type
	 * fill the empty objects by default objects
	 */
	public static function getQueriedObject($type = null, $defaultObjectID = null){

		$data = get_queried_object();

		switch($type){
			case "user":    //if not user fetched - get first user
				if(empty($data) || $data instanceof WP_User == false){
					if(!empty($defaultObjectID)){
						$data = get_user_by("id", $defaultObjectID);

						return ($data);
					}

					//get first object
					$arrUsers = get_users(array("number" => 1));
					if(empty($arrUsers))
						return (false);

					$data = $arrUsers[0];

					return ($data);
				}
			break;
		}

		return ($data);
	}

	/**
	 * check if archive location
	 */
	public static function isArchiveLocation(){

		if(is_singular())
			return (false);

		if((is_archive() || is_tax() || is_home() || is_search()))
			return (true);

		if(class_exists("UniteCreatorElementorIntegrate")){
			$templateType = UniteCreatorElementorIntegrate::getCurrentTemplateType();
			if($templateType == "archive")
				return (true);
		}

		return (false);
	}

	/**
	 * get max menu order
	 */
	public static function getMaxMenuOrder($postType, $parentID = null){

		$tablePosts = UniteProviderFunctionsUC::$tablePosts;

		$db = self::getDB();

		$query = "select MAX(menu_order) as maxorder from {$tablePosts} where post_type='$postType'";

		if(!empty($parentID)){
			$parentID = (int)$parentID;
			$query .= " and post_parent={$parentID}";
		}

		$rows = $db->fetchSql($query);

		$maxOrder = 0;
		if(count($rows) > 0)
			$maxOrder = $rows[0]["maxorder"];

		if(!is_numeric($maxOrder))
			$maxOrder = 0;

		return ($maxOrder);
	}

	/**
	 *
	 * get wp-content path
	 */
	public static function getPathUploads(){

		if(is_multisite()){
			if(!defined("BLOGUPLOADDIR")){
				$pathBase = self::getPathBase();
				$pathContent = $pathBase . "wp-content/uploads/";
			}else
				$pathContent = BLOGUPLOADDIR;
		}else{
			$pathContent = WP_CONTENT_DIR;
			if(!empty($pathContent)){
				$pathContent .= "/";
			}else{
				$pathBase = self::getPathBase();
				$pathContent = $pathBase . "wp-content/uploads/";
			}
		}

		return ($pathContent);
	}

	/**
	 *
	 * simple enqueue script
	 */
	public static function addWPScript($scriptName){

		wp_enqueue_script($scriptName);
	}

	/**
	 *
	 * simple enqueue style
	 */
	public static function addWPStyle($styleName){

		wp_enqueue_style($styleName);
	}

	/**
	 * add shortcode
	 */
	public static function addShortcode($shortcode, $function){

		add_shortcode($shortcode, $function);
	}

	/**
	 *
	 * add all js and css needed for media upload
	 */
	public static function addMediaUploadIncludes(){

		self::addWPScript("thickbox");
		self::addWPStyle("thickbox");
		self::addWPScript("media-upload");
	}

	/**
	 * check if post exists by title
	 */
	public static function isPostExistsByTitle($title, $postType = "page"){

		$post = get_page_by_title($title, ARRAY_A, $postType);

		return !empty($post);
	}

	/**
	 * tells if the page is posts of pages page
	 */
	public static function isAdminPostsPage(){

		$screen = get_current_screen();
		$screenID = $screen->base;
		if(empty($screenID))
			$screenID = $screen->id;

		if($screenID != "page" && $screenID != "post")
			return (false);

		return (true);
	}

	/**
	 *
	 * register widget (must be class)
	 */
	public static function registerWidget($widgetName){

		add_action('widgets_init', create_function('', 'return register_widget("' . $widgetName . '");'));
	}

	/**
	 * get admin title
	 */
	public static function getAdminTitle($customTitle){

		global $title;

		if(!empty($customTitle))
			$title = $customTitle;
		else
			get_admin_page_title();

		$title = esc_html(strip_tags($title));

		if(is_network_admin()){
			/* translators: Network admin screen title. 1: Network name */
			$admin_title = sprintf(__('Network Admin: %s'), esc_html(get_network()->site_name));
		}elseif(is_user_admin()){
			/* translators: User dashboard screen title. 1: Network name */
			$admin_title = sprintf(__('User Dashboard: %s'), esc_html(get_network()->site_name));
		}else{
			$admin_title = get_bloginfo('name');
		}

		if($admin_title == $title){
			/* translators: Admin screen title. 1: Admin screen name */
			$admin_title = sprintf(__('%1$s &#8212; WordPress'), $title);
		}else{
			/* translators: Admin screen title. 1: Admin screen name, 2: Network or site name */
			$admin_title = sprintf(__('%1$s &lsaquo; %2$s &#8212; WordPress'), $title, $admin_title);
		}

		return ($admin_title);
	}

	/**
	 * get all filters callbacks
	 */
	public static function getFilterCallbacks($tag){

		global $wp_filter;
		if(isset($wp_filter[$tag]) == false)
			return (array());

		$objFilter = $wp_filter[$tag];

		$arrCallbacks = $objFilter->callbacks;
		if(empty($arrCallbacks))
			return (array());

		return ($arrCallbacks);
	}

	/**
	 * get action functions of some tag
	 */
	public static function getActionFunctionsKeys($tag){

		$arrCallbacks = self::getFilterCallbacks($tag);

		foreach($arrCallbacks as $priority => $callbacks){
			$arrKeys = array_keys($callbacks);

			foreach($arrKeys as $key){
				$arrFunctions[$key] = true;
			}
		}

		return ($arrFunctions);
	}

	/**
	 * clear filters from functions
	 */
	public static function clearFiltersFromFunctions($tag, $arrFunctionsAssoc){

		global $wp_filter;
		if(isset($wp_filter[$tag]) == false)
			return (false);

		if(empty($arrFunctionsAssoc))
			return (false);

		$objFilter = $wp_filter[$tag];

		$arrFunctions = array();
		$arrCallbacks = $objFilter->callbacks;
		if(empty($arrCallbacks))
			return (array());

		foreach($arrCallbacks as $priority => $callbacks){
			$arrKeys = array_keys($callbacks);

			foreach($arrKeys as $key){
				if(isset($arrFunctionsAssoc[$key]))
					unset($wp_filter[$tag]->callbacks[$priority][$key]);
			}
		}
	}

	/**
	 * get blog url
	 */
	public static function getUrlBlog(){

		//home page:

		$showOnFront = get_option('show_on_front');
		if($showOnFront != "page"){
			$urlBlog = home_url();

			return ($urlBlog);
		}

		//page is missing:

		$pageForPosts = get_option('page_for_posts');
		if(empty($pageForPosts)){
			$urlBlog = home_url('/?post_type=post');

			return ($urlBlog);
		}

		//some page:
		$urlBlog = self::getPermalink($pageForPosts);

		return ($urlBlog);
	}

	/**
	 * get current page url
	 */
	public static function getUrlCurrentPage($isClear = false){

		global $wp;
		$urlPage = home_url($wp->request);

		if($isClear == false)
			return ($urlPage);

		$page = get_query_var("paged");

		if(empty($page))
			return ($urlPage);

		$urlPage = str_replace("/page/$page", "/", $urlPage);

		return ($urlPage);
	}

	/**
	 * get permalist with check of https
	 */
	public static function getPermalink($post){

		$url = get_permalink($post);
		if(GlobalsUC::$is_ssl == true)
			$url = UniteFunctionsUC::urlToSsl($url);

		return ($url);
	}

	/**
	 * tell wp plugins do not cache the page
	 */
	public static function preventCachingPage(){

		$arrNotCacheTags = array("DONOTCACHEPAGE", "DONOTCACHEDB", "DONOTMINIFY", "DONOTCDN");

		foreach($arrNotCacheTags as $tag){
			if(defined($tag))
				continue;

			define($tag, true);
		}

		nocache_headers();
	}

	/**
	 * get all action keys
	 */
	public static function getAllWPActionKeys($action){

		global $wp_filter;

		$initFuncs = UniteFunctionsUC::getVal($wp_filter, "admin_init");
		$callbacks = $initFuncs->callbacks;

		$arrAllKeys = array();

		foreach($callbacks as $arrCallbacks){
			$arrKeys = array_keys($arrCallbacks);

			$arrAllKeys = array_merge($arrAllKeys, $arrKeys);
		}

		return ($arrAllKeys);
	}


}  //end of the class

//init the static vars
UniteFunctionsWPUC::initStaticVars();

?>
