<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteCreatorObjectsListView extends UniteElementsBaseUC{
	
	protected $objectClass;
	protected $dbTable;
	protected $viewObject, $viewObjectPreview, $urlListBase;
	protected $actionDelete;
	
	protected $enableCategories = false;
	
	protected $defaultOrderBy = "title";
	protected $sizeActions = 380;
	protected $txtSingleName = "Object";		//for phrases like "Search Template"
	protected $txtMultipleName = "Objects";		//name for multiple objects phrases like: "templates"
	
	private $arrObjects, $objTable, $numObjects;
	
	
	/**
	 * validate that all inited
	 */
	private function validateInited(){
		
		if(empty($this->objectClass))
			UniteFunctionsUC::throwError("The object class should be inited");
		
		UniteFunctionsUC::validateNotEmpty($this->dbTable, "DB Table");
		UniteFunctionsUC::validateNotEmpty($this->urlListBase, "Url List Base");
		UniteFunctionsUC::validateNotEmpty($this->viewObject, "Single object view");
		UniteFunctionsUC::validateNotEmpty($this->viewObjectPreview, "Single Object Preview View");
		UniteFunctionsUC::validateNotEmpty($this->actionDelete, "Action Delete");
		
	}
	
	
	/**
	 * validate object methods
	 */
	private function validateObjectMethods($object){

		$arrMethods = array("initByID","getID","getTitle");
		foreach($arrMethods as $method)
			UniteFunctionsUC::validateObjectMethod($object, $method, $this->objectClass);
		
	}
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		parent::__construct();
		
		$this->validateInited();
		
		$this->display();
	}

	
	private function a_PUT_HTML(){}
	
	
	/**
	 * put html before table
	 */
	protected function putHtml_beforeTable(){

		$this->objTable->putActionsFormStart();
		$isPutSearchForm = ($this->numObjects > 5);
		
		$textSearch = "Search"." ".$this->txtSingleName;
		
		//if less then 5, put only if the search phrase exists
		$this->objTable->putSearchForm($textSearch, esc_html__("Clear","unlimited-elements-for-elementor"), $isPutSearchForm);
		
	}
	
	/**
	 * put html after table
	 */
	protected function putHtml_afterTable(){
		
		$this->objTable->putPaginationHtml();				
		$this->objTable->putInpageSelect();
		
		$this->objTable->putActionsFormEnd();
		
		if($this->enableCategories)
			$this->putDialogCategories();
		
	}
	
	/**
	 * get client side options
	 */
	protected function getClientSideOptions(){
		
		$options = array();
		$options["enable_categories"] = $this->enableCategories;
		$options["action_delete"] = $this->actionDelete;
		
		return($options);
	}
		
	
	/**
	 * put table html
	 */
	protected function putHtml_table(){
		
		$txtDelete = esc_html__("Are you sure you want to delete this", "unlimited-elements-for-elementor")." ".$this->txtSingleName;
		
		$options = $this->getClientSideOptions();
		$strOptions = UniteFunctionsUC::jsonEncodeForHtmlData($options);
		
		?>
			<div id="uc_table_objects_wrapper" data-options="<?php echo UniteProviderFunctionsUC::escAddParam($strOptions)?>">
			
				<?php if(empty($this->arrObjects)): ?>
				<div>
					<?php 
						$txtNotFound = esc_html__("No","unlimited-elements-for-elementor")." ".$this->txtMultipleName." ".esc_html__("Found","unlimited-elements-for-elementor");
						echo esc_html($txtNotFound);
					?>
				</div>			
				<?php else:?>
				
				<table id="uc_table_objects" class='unite_table_items' data-text-confirm-delete="<?php echo esc_attr($txtDelete)?>">
					<thead>
						<tr>
							<th width=''>
								<?php $this->objTable->putTableOrderHeader("title", $this->txtSingleName." ".esc_html__("Title", "unlimited-elements-for-elementor")) ?>
							</th>
							
							<?php if($this->enableCategories == true):?>
							<th width='200'><?php $objTable->putTableOrderHeader("catid", esc_html__("Category","unlimited-elements-for-elementor")) ?>
							<?php endif?>
							
							<th width='<?php echo esc_attr($sizeActions)?>'><?php esc_html_e("Actions","unlimited-elements-for-elementor"); ?></th>
							<th width='60'><?php esc_html_e("Preview","unlimited-elements-for-elementor"); ?></th>
						</tr>
						
						<?php foreach($this->arrObjects as $key=>$object):
								
								$id = $object->getID();
								$title = $object->getTitle();
								$editLink = HelperUC::getViewUrl($this->viewObject,"id=".$id);
								$previewLink = HelperUC::getViewUrl($this->viewObjectPreview,"id=".$id, true);
								
								$showTitle = HelperHtmlUC::getHtmlLink($editLink, $title);
								$rowClass = ($key%2==0)?"unite-row1":"unite-row2";
								
								if($this->enableCategories){
									dmp("check the getCategoryInfo function");
									$arrCategory = $object->getCategoryInfo();
									$catID = UniteFunctionsUC::getVal($arrCategory, "id");
									$catTitle = UniteFunctionsUC::getVal($arrCategory, "title");
									
									exit();
								}
								
						?>
						<tr class="<?php echo esc_attr($rowClass)?>">
							<td><?php echo esc_html($showTitle)?></td>
							
							<?php if($this->enableCategories):?>
							<td><a href="javascript:void(0)" class="uc-layouts-list-category" data-layoutid="<?php echo esc_attr($id)?>" data-catid="<?php echo esc_attr($catID)?>" data-action="manage_category"><?php echo esc_html($catTitle)?></a></td>
							<?php endif?>
							
							<td>
								<a href='<?php echo esc_attr($editLink)?>' class="unite-button-primary float_left mleft_15"><?php echo esc_html__("Edit", "unlimited-elements-for-elementor")." ".$this->txtSingleName ?></a>
								
								<a href='javascript:void(0)' data-action="delete" data-id="<?php echo esc_attr($id)?>" class="uc-button-action unite-button-secondary float_left mleft_15"><?php esc_html_e("Delete","unlimited-elements-for-elementor"); ?></a>
								<span class="loader_text uc-loader-delete" style="display:none"><?php esc_html_e("Deleting", "unlimited-elements-for-elementor")?></span>

								<!-- 
								<a href='javascript:void(0)' data-layoutid="<?php echo esc_attr($id)?>" data-id="<?php echo esc_attr($id)?>" class="button_duplicate unite-button-secondary float_left mleft_15"><?php esc_html_e("Duplicate","unlimited-elements-for-elementor"); ?></a>
								<span class="loader_text uc-loader-duplicate" style="display:none"><?php esc_html_e("Duplicating", "unlimited-elements-for-elementor")?></span>
								
								<a href='javascript:void(0)' data-layoutid="<?php echo esc_attr($id)?>" data-id="<?php echo esc_attr($id)?>" class="button_export unite-button-secondary float_left mleft_15"><?php esc_html_e("Export","unlimited-elements-for-elementor"); ?></a>
								-->
								
							</td>
							<td>
								<a href='<?php echo esc_attr($previewLink)?>' target="_blank" class="unite-button-secondary float_left"><?php esc_html_e("Preview","unlimited-elements-for-elementor"); ?></a>
							</td>
							
						</tr>							
						
						<?php endforeach?>
					</thead>
				
				</table>
								
				<?php endif;?>
			</div>
			<?php 
	}
	
	
	/**
	 * put scripts
	 */
	protected function putHtml_scripts(){
		
		?>
		<script type="text/javascript">
		
			jQuery(document).ready(function(){
				
				var objAdmin = new UniteCreatorAdmin_ObjectsList();
				objAdmin.initObjectsListView();
				
			});
		
		</script>
		
		<?php 
	}
	
	
	/**
	 * put html
	 */
	protected function putHtml(){
		
		$txtDelete = esc_html__("Are you sure you want to delete this", "unlimited-elements-for-elementor")." ".$this->txtSingleName;
		
		?>
			<div class="unite-content-wrapper">
				
				<?php 
					$this->putHtml_beforeTable();
					$this->putHtml_table();
					$this->putHtml_afterTable();
					$this->putHtml_scripts();
				?>
			</div>
			
		<?php 
	}
	
	private function a_INIT(){}
	
	/**
	 * convert records to layouts objects
	 */
	private function recordsToObjects($records){
				
		$arrObjects = array();
		foreach($records as $index=>$record){
			$objectID = UniteFunctionsUC::getVal($record, "id");
						
			$object = new $this->objectClass();
			
			//validate single object methods
			if($index == 0)
				$this->validateObjectMethods($object);
							
			$object->initByID($objectID);
			
			$arrObjects[] = $object;
		}
		
		
		return($arrObjects);
	}
	
	
	
	/**
	 * get records with paging
	 */
	protected function getRecordsPaging($pagingOptions){
		
		$where = "";
				
		//search
		$search = UniteFunctionsUC::getVal($pagingOptions, "search");
		
		if(!empty($search))	{
			$search = $this->db->escape($search);
			$where = "title LIKE '%{$search}%'";
		}
		
		$order = UniteFunctionsUC::getVal($pagingOptions, "ordering");
		
		$response = $this->db->fetchPage($this->dbTable, $pagingOptions, $where, $order);
		
		return($response);
	}
	
	
	/**
	 * get layouts with paging data
	 */
	protected function initAllObjects(){
		
		$this->objTable = new UniteTableUC();
		$this->objTable->setDefaultOrderby($this->defaultOrderBy);
		
		$pagingOptions = $this->objTable->getPagingOptions();
		
		$response = $this->getRecordsPaging($pagingOptions);
		
		$rows = $response["rows"];
		unset($response["rows"]);
		
		$this->arrObjects = $this->recordsToObjects($rows);
		$this->numObjects = count($this->arrObjects);
		
		$this->objTable->setPagingData($this->urlListBase, $response);
		
	}
	
	/**
	 * put script inlcudes
	 */
	protected function putScriptIncludes(){
		
		HelperUC::addScript("unitecreator_admin_objectslist", "unitecreator_admin_objectslist.js");
	}
	
	
	/**
	 * constructor
	 */
	public function display(){
						
		$this->initAllObjects();
		$this->putScriptIncludes();
		
		$this->putHtml();
	}
	
}

