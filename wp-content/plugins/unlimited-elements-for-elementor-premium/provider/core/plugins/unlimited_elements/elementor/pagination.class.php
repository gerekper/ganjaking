<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

//no direct accees
defined ('UNLIMITED_ELEMENTS_INC') or die ('restricted aceess');

class UniteCreatorElementorPagination{
	
	const SHOW_DEBUG = false;		//please turn it off
	
	/**
	 * add content controls
	 */
	private function addElementorControls_content($widget, $postListParam){
		
		$condition = UniteFunctionsUC::getVal($postListParam, "condition");
		
		$isFilterable = UniteFunctionsUC::getVal($postListParam, "is_filterable");
		$isFilterable = UniteFunctionsUC::strToBool($isFilterable);
		
		$enableAjax = UniteFunctionsUC::getVal($postListParam, "enable_ajax");
		$enableAjax = UniteFunctionsUC::strToBool($enableAjax);
		
		$disablePagination = UniteFunctionsUC::getVal($postListParam, "disable_pagination");
		$disablePagination = UniteFunctionsUC::strToBool($disablePagination);
		
		if($disablePagination === true)
			return(false);
		
		
		$paramName = UniteFunctionsUC::getVal($postListParam, "name");		
		
		$textSection = esc_html__("Posts Pagination", "unlimited-elements-for-elementor");
		if($isFilterable == true)
			$textSection = esc_html__("Posts Pagination and Filtering", "unlimited-elements-for-elementor");
		
		if($enableAjax == true)
			$textSection = esc_html__("Posts Pagination and Filtering", "unlimited-elements-for-elementor");
		
		$arrSectionSettings = array(
             'label' => $textSection,
		);
		
		if(!empty($condition))
			$arrSectionSettings["condition"] = $condition;
			
		$widget->start_controls_section(
                'section_pagination', $arrSectionSettings
         );
		
		$widget->add_control(
			'pagination_heading',
			[
				'label' => __( 'When turned on, the pagination will appear in archive or single pages, you have option to use the "Posts Pagination" widget for all the styling options', "unlimited-elements-for-elementor"),
				'type' => \Elementor\Controls_Manager::HEADING,
				'default' => ''
			]
		);
         
         
		$widget->add_control(
			'pagination_type',
			[
				'label' => __( 'Pagination', "unlimited-elements-for-elementor"),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', "unlimited-elements-for-elementor"),
					'numbers' => __( 'Numbers', "unlimited-elements-for-elementor"),
					'pagination_widget' => __( 'Using Pagination Widget', "unlimited-elements-for-elementor")
				],
			]
		);

		//add filter enabled controls
		
		if($enableAjax == true){
			
			$widget->add_control(
				$paramName.'_isajax',
				[
					'label' => __( 'Enable Post Filtering', "unlimited-elements-for-elementor"),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Yes', 'unlimited-elements-for-elementor' ),
					'label_off' => __( 'No', 'unlimited-elements-for-elementor' ),
					'return_value' => 'true',
					'default' => '',
					'separator' => 'before',
					'description'=>__('When turned on, you can use all the post filters widgets like tabs filter, load more etc with this grid', 'unlimited-elements-for-elementor')
				]
			);
			
			$widget->add_control(
				$paramName.'_ajax_seturl',
				array(
					'label' => __( 'Filters Behaviour', "unlimited-elements-for-elementor"),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'ajax',
					'options' => array(
						'ajax' => __( 'Ajax', "unlimited-elements-for-elementor"),
						//'url' => __( 'Url Change Only', "unlimited-elements-for-elementor"),
						'mixed' => __( 'Ajax and Url Change', "unlimited-elements-for-elementor"),
						'mixed_back' => __( 'Ajax, Url Change and Back Button', "unlimited-elements-for-elementor")
					),
					'condition' => array($paramName.'_isajax'=>"true"),
					'description'=>__('Choose the filters behaviour for the current grid. If third mode selected - after ajax it will remember the current grid and filters state in the url so you can get back to it later', 'unlimited-elements-for-elementor')
				)
			);
			
			
		}
                  
        $widget->end_controls_section();
	}
	
	
	/**
	 * add styles controls
	 */
	private function addElementorControls_styles($widget){
		
		$widget->start_controls_section(
			'section_pagination_style',
			[
				'label' => __( 'Pagination', "unlimited-elements-for-elementor"),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'pagination_type!' => '',
				],
			]
		);

		$widget->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'pagination_typography',
				'selector' => '{{WRAPPER}} .uc-posts-pagination',
				'scheme' => \Elementor\Scheme_Typography::TYPOGRAPHY_2,
			]
		);

		$widget->add_control(
			'pagination_color_heading',
			[
				'label' => __( 'Colors', "unlimited-elements-for-elementor"),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$widget->start_controls_tabs( 'pagination_colors' );
		
		$widget->start_controls_tab(
			'pagination_color_normal',
			[
				'label' => __( 'Normal', "unlimited-elements-for-elementor"),
			]
		);

		$widget->add_control(
			'pagination_color',
			[
				'label' => __( 'Color', "unlimited-elements-for-elementor"),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .uc-posts-pagination .page-numbers:not(.dots)' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->end_controls_tab();
		
		$widget->start_controls_tab(
			'pagination_color_hover',
			[
				'label' => __( 'Hover', "unlimited-elements-for-elementor"),
			]
		);

		$widget->add_control(
			'pagination_hover_color',
			[
				'label' => __( 'Color', "unlimited-elements-for-elementor"),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .uc-posts-pagination a.page-numbers:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'pagination_color_active',
			[
				'label' => __( 'Active', "unlimited-elements-for-elementor"),
			]
		);

		$widget->add_control(
			'pagination_active_color',
			[
				'label' => __( 'Color', "unlimited-elements-for-elementor"),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .uc-posts-pagination .page-numbers.current' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->end_controls_tab();

		$widget->end_controls_tabs();

		$widget->add_responsive_control(
			'pagination_spacing',
			[
				'label' => __( 'Space Between', "unlimited-elements-for-elementor"),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'separator' => 'before',
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}} .uc-posts-pagination .page-numbers:not(:first-child)' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'body:not(.rtl) {{WRAPPER}} .uc-posts-pagination .page-numbers:not(:last-child)' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'body.rtl {{WRAPPER}} .uc-posts-pagination .page-numbers:not(:first-child)' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'body.rtl {{WRAPPER}} .uc-posts-pagination .page-numbers:not(:last-child)' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
				],
			]
		);

		$widget->end_controls_section();
		
	}
	
	
	/**
	 * add elementor controls
	 */
	public function addElementorSectionControls($widget, $postListParam = null){
				
		$this->addElementorControls_content($widget,$postListParam);
		
		//$this->addElementorControls_styles($widget);
		
	}
	
	
	/**
	 * put pagination
	 */
	public function getHTMLPaginationByElementor($arrValues, $isArchivePage){
		
		$paginationType = UniteFunctionsUC::getVal($arrValues, "pagination_type");
				
		if($paginationType != "numbers")
			return(false);

		if(is_front_page() == true)
			return(false);
				
		$options = array();
		$options["prev_next"] = false;
				
		//$options["mid_size"] = 2;
		//$options["prev_text"] = __( 'Newer', "unlimited-elements-for-elementor");
		//$options["next_text"] = __( 'Older', "unlimited-elements-for-elementor");
		//$options["total"] = 10;
		//$options["current"] = 3;
		
		if($isArchivePage == true){

			$options = $this->getArchivePageOptions($options);
		
			$pagination = get_the_posts_pagination($options);
		}
		else{
			
			$options = $this->getSinglePageOptions($options);
			
			if(isset($options["current"]) == false)
				return(false);
			
			$pagination = paginate_links($options);
		}
		
		$html = "<div class='uc-posts-pagination'>$pagination</div>";
		
		return($html);
	}
	
	/**
	 * get archive options
	 */
	private function getArchivePageOptions($options){
		
		//output demo pagination
		$isEditMode = UniteCreatorElementorIntegrate::$isEditMode;
		if($isEditMode == true){
			$options["total"] = 5;
			$options["current"] = 2;
			return($options);
		}
		
		return($options);
	}
	
	
	/**
	 * get current page
	 */
	private function getCurrentPage(){
		
		//return by ucpage in case ajax request
		
		$objFilters = new UniteCreatorFiltersProcess();
		$isFrontAjax = $objFilters->isFrontAjaxRequest();
		
		if($isFrontAjax == true){
			
			$ucpage = UniteFunctionsUC::getGetVar("ucpage","",UniteFunctionsUC::SANITIZE_TEXT_FIELD);
			$ucpage = (int)$ucpage;
			
			if(!empty($ucpage))
				return($ucpage);
		}
		
		$currentPage = 1;
		if(!empty(GlobalsProviderUC::$lastPostQuery_page)){
			
			$currentPage = GlobalsProviderUC::$lastPostQuery_page;
		}
		else{
			$currentPage = get_query_var("page");			
		}
		
		$currentPage = (int)$currentPage;
		
		return($currentPage);
	}
	
	/**
	 * get total pages from current query
	 */
	private function getTotalPages(){
				
		if(empty(GlobalsProviderUC::$lastPostQuery))
			return(0);
		
		$numPages = GlobalsProviderUC::$lastPostQuery->max_num_pages;
		if($numPages <= 1)
			return(0);
		
		$numPages = (int)$numPages;
		
		return($numPages);
	}
	
	
	/**
	 * get single page options
	 */
	private function getSinglePageOptions($options, $forceFormat = null){
		
		//output demo pagination
		$isEditMode = UniteCreatorElementorIntegrate::$isEditMode;
				
		if($isEditMode == true){
			
			if(self::SHOW_DEBUG == true){
				dmp("edit mode!!!");
			}
			
			$options["total"] = 5;
			$options["current"] = 2;
			return($options);
		}
		
		if(empty(GlobalsProviderUC::$lastPostQuery))
			return($options);
		
		$numPages = GlobalsProviderUC::$lastPostQuery->max_num_pages;
		if($numPages <= 1)
			return($options);
		
		global $wp_rewrite;
		$isUsingPermalinks = $wp_rewrite->using_permalinks();
		
		if( $isUsingPermalinks == true){		//with permalinks - add /2
			
			$permalink = get_permalink();
			
			$isFront = is_front_page();
			$isArchive = is_archive();
			
			if($isFront == true)
				$permalink = GlobalsUC::$url_base;
			
			$urlCurrentPage = UniteFunctionsWPUC::getUrlCurrentPage(true);
			
			if($isArchive == true)
				$permalink = $urlCurrentPage;
			
			$options['base'] = trailingslashit( $permalink ) . '%_%';
			$options['format'] = user_trailingslashit( '%#%', 'single_paged' );
			
			if($isFront || $isArchive || $forceFormat == "page")
				$options['format'] = user_trailingslashit( 'page/%#%', 'single_paged' );
			
		}else{		//if not permalinks
			$options['format'] = '?page=%#%';		// add ?page=2
		}
		
		$options["total"] = $numPages;
		
		//set current page
		$currentPage = 1;
		if(!empty(GlobalsProviderUC::$lastPostQuery_page)){
			$currentPage = GlobalsProviderUC::$lastPostQuery_page;
			
			if(self::SHOW_DEBUG == true){
				dmp("current: $currentPage from the lastPostQuery_page var");
			}
			
		}
		else{
			$currentPage = get_query_var("paged");
			dmp("current: $currentPage from the get_query_var");
		}
				
		if(empty($currentPage))
			$currentPage = 1;
		
		$options["current"] = $currentPage;
		
		return($options);		
	}
	
	/**
	 * get has more by last post query
	 */
	private function getNextOffsetByQuery(){
		
		//define has more by last post query
		$foundPosts = GlobalsProviderUC::$lastPostQuery->found_posts;
		
		if(empty($foundPosts))
			return(-1);
		
		$numPosts = GlobalsProviderUC::$lastPostQuery->post_count;

		$queryVars = GlobalsProviderUC::$lastPostQuery->query_vars;
		
		$offset = UniteFunctionsUC::getVal($queryVars, "offset");
		
		if(empty($offset))
			$offset = 0;
		
		$lastPost = $offset + $numPosts;
		
		if($lastPost >= $foundPosts)
			return(-1);
		
		return($lastPost);
	}
	
	
	/**
	 * get last request paging data
	 */
	public function getPagingData(){
		
		$currentPage = $this->getCurrentPage();
		$totalPages = $this->getTotalPages();
		
		$nextOffset = null;
		
		if(GlobalsProviderUC::$lastPostQuery){
			
			$currentOffset = GlobalsProviderUC::$lastPostQuery_offset;
			
			$nextOffset = $this->getNextOffsetByQuery();
			
			$hasMore = $nextOffset >= 0;
			
		}else{
			
			$hasMore = false;
			if(!empty($currentPage) && !empty($totalPages) && $currentPage < $totalPages)
				$hasMore = true;
		}
		
		$nextPage = $currentPage+1;
		
		$output = array();		
		$output["current"] = $currentPage;
		$output["next"] = $nextPage;
		$output["total"] = $totalPages;
		$output["has_more"] = $hasMore;
		
		if($hasMore == true)
			$output["next_offset"] = $nextOffset;
		
		return($output);
	}
	
	
	/**
	 * put pagination widget html
	 */
	public function putPaginationWidgetHtml($args){
		
		$putPrevNext = UniteFunctionsUC::getVal($args, "put_prev_next_buttons");
		$putPrevNext = UniteFunctionsUC::strToBool($putPrevNext);
		
		$midSize = UniteFunctionsUC::getVal($args, "mid_size", 2);
		$midSize = (int)$midSize;

		$endSize = UniteFunctionsUC::getVal($args, "end_size", 1);
		$endSize = (int)$endSize;
		
		$showAll = UniteFunctionsUC::getVal($args, "show_all");
		$showAll = UniteFunctionsUC::strToBool($showAll);
		
		$isShowText = UniteFunctionsUC::getVal($args, "show_text");
		$isShowText = UniteFunctionsUC::strToBool($isShowText);
		
		$prevText = UniteFunctionsUC::getVal($args, "prev_text");
		$nextText = UniteFunctionsUC::getVal($args, "next_text");
		
		$prevText = trim($prevText);
		$nextText = trim($nextText);
		
		$isDebug = UniteFunctionsUC::getVal($args, "debug_pagination_options");
		$isDebug = UniteFunctionsUC::strToBool($isDebug);
		
		if(self::SHOW_DEBUG == true)
			$isDebug = true;
		
		$forceFormat = UniteFunctionsUC::getVal($args, "force_format");
		if($forceFormat == "none")
			$forceFormat = null;
		
		//--------- prepare options
		
		$options = array();
		
		$options["show_all"] = $showAll;
		$options["mid_size"] = $midSize;
		$options["end_size"] = $endSize;
		
		$options["prev_next"] = $putPrevNext;
		
		if(!empty($prevText))
			$options["prev_text"] = $prevText;
		
		if(!empty($nextText))
			$options["next_text"] = $nextText;

		if(empty($nextText))
			$options["next_text"] = _x( 'Next', 'next set of posts' );
		
		if(empty($prevText))
			$options["prev_text"] = _x( 'Previous', 'previous set of posts' );
		
		//disable the text, leave only icon
		if($isShowText == false){
						
			$options["next_text"] = "";
			$options["prev_text"] = "";
		}
		
		//$options["total"] = 10;
		//$options["current"] = 3;
		
		
		//-------- put pagination html
		
		$isArchivePage = UniteFunctionsWPUC::isArchiveLocation();
				
		if($isDebug == true){
			echo "<div class='uc-pagination-debug'>";
			
			dmp("is archive (original): ".$isArchivePage);
			
			if(!empty($forceFormat))
				dmp("Force Format: ".$forceFormat);
		}
		
		//on ajax - always take the last grid response - single
		
		$objFilters = new UniteCreatorFiltersProcess();
		$isAjax = $objFilters->isFrontAjaxRequest();
		
		if($isAjax == true)
			$isArchivePage = false;
		
		
		//fix the archive
		
		if($isArchivePage == true && !empty(GlobalsProviderUC::$lastPostQuery_paginationType) && GlobalsProviderUC::$lastPostQuery_paginationType != GlobalsProviderUC::QUERY_TYPE_CURRENT){
			
			$isArchivePage = false;
			
			if($isDebug == true){
				dmp("last pagination type: ");
				dmp(GlobalsProviderUC::$lastPostQuery_paginationType);
				
				dmp("change to custom");
			}
		}
			
		//force format yes/no
		
		switch($forceFormat){
			case "archive":
				$isArchivePage = true;
			break;
			case "custom":
				$isArchivePage = false;
			break;
		}
		
		if($isArchivePage == true){
			
			$options = $this->getArchivePageOptions($options);
			$pagination = get_the_posts_pagination($options);
			
			//put debug
			if($isDebug == true){
				dmP("Archive Pagination");
				
				global $wp_query;
				
				if(!empty($wp_query)){
					
					$queryVars = $wp_query->query_vars;
					
					$queryVars = UniteFunctionsWPUC::cleanQueryArgsForDebug($queryVars);
					
					dmp("Current query vars");
					dmp($queryVars);
					
					dmp("max pages: ".$wp_query->max_num_pages);					
					
					//$currentPage = $this->getCurrentPage();
					//dmp("current page: ".$currentPage);
					
				}
								
			}
			
		}else{		//on single
			
			//skip for home pages
			$options = $this->getSinglePageOptions($options, $forceFormat);
			
			if($isDebug == true){
				
				dmp("custom query pagination");
			}
			
			if(isset($options["current"]) == false){
				
				if($isDebug == true){
					dmp("<b>Pagination Options (custom) </b>: <br>");
					dmp("No pagination found for the last query <br>");
					dmp($options);
				}
				
				return(false);
			}
													
			$pagination = paginate_links($options);	
		}
		
		if($isDebug == true){
			
			dmp("<b>Pagination Options</b>: ");
			dmp($options);
			
			echo "</div>";
		}
		
		echo $pagination;
	}
	
	/**
	 * get load more data
	 */
	public function getLoadmoreData($isEditMode = false){
		
		//editor mode
		if($isEditMode == true){
						
			$output = array();
			$output["attributes"] = "";
			$output["style"] = "";
			$output["more"] = true;
			
			return($output);
		}
		
		$arrPagingData = $this->getPagingData();
		
		$hasMore = UniteFunctionsUC::getVal($arrPagingData, "has_more");
		
		$nextPage = UniteFunctionsUC::getVal($arrPagingData, "next"); 
		
		$nextOffset = UniteFunctionsUC::getVal($arrPagingData, "next_offset"); 
		
		$attributes = "";
		$style = "";
					
		if($hasMore == true){
			$attributes = "data-more=\"$hasMore\"";
			
			if(!empty($nextOffset))
				$attributes .= " data-nextoffset=\"$nextOffset\" ";
			else
				$attributes .= " data-nextpage=\"$nextPage\" ";
			
		}
		else
			$style = "style='display:none'";
		
		
		$output = array();
		$output["attributes"] = $attributes;
		$output["style"] = $style;
		$output["more"] = $hasMore;
		
		
		return($output);
	}
	
	
}