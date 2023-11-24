<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use \Elementor\Utils;

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteCreatorElementorControls{

	
	
	/**
	 * get repeater default items
	 */
	private function getRepeaterDefaultItems($objAddon){
    	
		$arrItems = array();
		
		$urlImages = GlobalsUC::$urlPluginImages;
		
		$arrItems[] = array("item_type"=>"image",
							"image"=> array("id"=>0,"url"=>$urlImages."gallery1.jpg") );
		
		$arrItems[] = array("item_type"=>"image",
							"image"=> array("id"=>0,"url"=>$urlImages."gallery2.jpg") );
		
		$arrItems[] = array("item_type"=>"youtube",
							"image"=> array("id"=>0,"url"=>$urlImages."gallery3.jpg"),
							"url_youtube"=>"qrO4YZeyl0I"
		);
		
		$arrItems[] = array("item_type"=>"image",
							"image"=> array("id"=>0,"url"=>$urlImages."gallery5.jpg") );
		
		$arrItems[] = array("item_type"=>"vimeo",
							"image"=> array("id"=>0,"url"=>$urlImages."gallery4.jpg"),
							"vimeo_id"=>"581014653");
		
		$arrItems[] = array("item_type"=>"html5",
							"image"=> array("id"=>0,"url"=>$urlImages."gallery6.jpg"),
							"url_html5"=> "http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4");
		
		$arrItems[] = array("item_type"=>"image",
							"image"=> array("id"=>0,"url"=>$urlImages."gallery1.jpg") );
		
    	    	
    	return($arrItems);
	}

	
	/**
	 * add repeater control
	 */
	public function addGalleryImageVideoRepeater($objControls, $textPrefix, $name, $listingParam, $objAddon){
		
		$isEnableVideo = UniteFunctionsUC::getVal($listingParam, "gallery_enable_video");
		
		$arrDefaultItems = $this->getRepeaterDefaultItems($objAddon);
		
		$repeater = new Repeater();
		
	        $objControls->start_controls_section(
	                'uc_section_listing_gallery_repeater', array(
	                'label' => $textPrefix.__(" Items", "unlimited-elements-for-elementor"),
	        		'condition'=>array($name."_source"=>"image_video_repeater")
	              )
	        );

	        // ----  item type ---------  
	        
	        if($isEnableVideo == true):
	        
				$repeater->add_control(
					'item_type',
						array(
							'label' => __( 'Item Type', 'unlimited-elements-for-elementor' ),
							'type' => Controls_Manager::SELECT,
							'default' => 'image',
							'options' => array(
								'image'  => __( 'Image', 'unlimited-elements-for-elementor' ),
								'youtube' => __( 'Youtube', 'unlimited-elements-for-elementor' ),
								'vimeo' => __( 'Vimeo', 'unlimited-elements-for-elementor' ),
								'wistia' => __( 'Wistia', 'unlimited-elements-for-elementor' ),
								'html5' => __( 'HTML5 Video', 'unlimited-elements-for-elementor' )
							)
						)
				);	    
				

			//--------- youtube url --------
			
			$repeater->add_control(
				'url_youtube',
				array(
					'label' => __( 'Youtube Url or ID', 'unlimited-elements-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => __( 'qrO4YZeyl0I', 'unlimited-elements-for-elementor' ),
					'description'=>'For example: https://www.youtube.com/watch?v=qrO4YZeyl0I or qrO4YZeyl0I',
					'separator'=>'before',
					'label_block'=>true,
					'condition'=>array('item_type'=>'youtube')
				)
			);

			//--------- vimeo id --------
			
			$repeater->add_control(
				'vimeo_id',
				array(
					'label' => __( 'Vimeo Video ID or Url', 'unlimited-elements-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => __( '581014653', 'unlimited-elements-for-elementor' ),
					'description'=>__('For example: 581014653, or https://vimeo.com/581014653','unlimited-elements-for-elementor'),
					'separator'=>'before',
					'label_block'=>true,
					'condition'=>array('item_type'=>'vimeo')
				)
			);
			
			//--------- wistia --------
			
			$repeater->add_control(
				'wistia_id',
				array(
					'label' => __( 'Wistia Video ID', 'unlimited-elements-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => __( '9oedgxuciv', 'unlimited-elements-for-elementor' ),
					'description'=>__('For example: 9oedgxuciv','unlimited-elements-for-elementor'),
					'separator'=>'before',
					'label_block'=>true,
					'condition'=>array('item_type'=>'wistia')
				)
			);
			
			//--------- html5 video --------
			
			$repeater->add_control(
				'url_html5',
				array(
					'label' => __( 'MP4 Video Url', 'unlimited-elements-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => __( 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4', 'unlimited-elements-for-elementor' ),
					'description'=>__('Enter url of the mp4 video in current or external site. Example: http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4','unlimited-elements-for-elementor'),
					'separator'=>'before',
					'label_block'=>true,
					'condition'=>array('item_type'=>'html5')
				)
			);
			
	        endif;	//enable video
			
			
			//--------- image --------
			
			$repeater->add_control(
				'image',
				array(
					'label' => __( 'Choose Image', 'unlimited-elements-for-elementor' ),
					'type' => Controls_Manager::MEDIA,
					'separator'=>'before',
					'default' => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'dynamic'=>array('active'=>true),
					'recursive'=>true
				)
			);			
			
			//--------- image heading --------
			if($isEnableVideo == true)
				$repeater->add_control(
					'image_heading_text',
					array(
						'label' => __( 'This image will be used for gallery thumbnail and video placeholder', 'unlimited-elements-for-elementor' ),
						'type' => Controls_Manager::HEADING,
						'condition'=>array('item_type!'=>'image')
					)
				);			
			
			//--------- title --------
			
			$repeater->add_control(
				'title',
				array(
					'label' => __( 'Item Title', 'unlimited-elements-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => __( '', 'unlimited-elements-for-elementor' ),
					'label_block'=>true,
					'separator'=>'before',
					'dynamic'=>array('active'=>true),
					'recursive'=>true
				)
			);

			//--------- description --------
			
			$repeater->add_control(
				'description',
				array(
					'label' => __( 'Item Description', 'unlimited-elements-for-elementor' ),
					'type' => Controls_Manager::TEXTAREA,
					'default' => __( '', 'unlimited-elements-for-elementor' ),
					'label_block'=>true,
					'dynamic'=>array('active'=>true),
					'recursive'=>true
				)
			);
			
			$arrControl["recursive"] = true;
			
			//--------- link --------
			
			$repeater->add_control(
				'link',
				array(
					'label' => __( 'Item Link', 'unlimited-elements-for-elementor' ),
					'type' => Controls_Manager::URL,
					'default' => array(
						'url' => '',
						'is_external' => true,
						'nofollow' => false),
					'label_block'=>true,
					'dynamic'=>array('active'=>true),
					'recursive'=>true
				)
			);
			
			
					
			$objControls->add_control(
				$name.'_items',
				array(
					'label' => __( 'Gallery Items', 'unlimited-elements-for-elementor' ),
					'type' => Controls_Manager::REPEATER,
					'fields' => $repeater->get_controls(),
					'default' => $arrDefaultItems,
					'title_field' => '{{{ title }}}',
				)
			);		
	        
	        $objControls->end_controls_section();
		
	}

}