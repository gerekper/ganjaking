<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_view_back extends WYSIJA_view{

    var $column_actions=array();/*list of actions possible through links in the list*/
    var $column_action_list='';/*name of the column that will contain the list of action*/
    var $arrayMenus=array();
    var $bulks=array();
    var $action='';
    var $statuses=array();
    var $skip_header = false; // simply returns the wrapper if true
    var $listingHeader = '';
    var $hiddenFields = '';

    function __construct(){
        /* the default actions to be linked in a listing */
        if(!$this->column_actions)  $this->column_actions=array('view'=>__('View',WYSIJA),'edit'=>__('Edit',WYSIJA),'delete'=>__('Delete',WYSIJA));

        $this->bulks["delete"]=array("name"=>__("Delete",WYSIJA));
    }

    /**
     * creation of a generic listing view
     * @param type $data
     */
    function main($data){

        echo '<form method="post" action="admin.php?page='.  esc_attr($_REQUEST['page']).'" id="posts-filter">';
        $this->filtersLink();
        $this->searchBox();

        $this->listing($data);
        echo '</form>';
    }

    function menuTop($actionmenu=false){

        $menu="";
        if(!empty($this->arrayMenus)){
           foreach($this->arrayMenus as $action =>$actiontrans){
                $menu.= '<a href="admin.php?page='.  esc_attr($_REQUEST['page']).'&action='.$action.'" class="button-secondary2">'.$actiontrans.'</a>';
            }
        }

        return $menu;
    }

    /**
     * to help reproduce the standard view of wordpress admin view here is the header part
     * @param type $methodView
     */
    function header($data=''){
        echo '<div id="wysija-app" class="wrap">';/*start div class wrap*/

        if($this->skip_header === true) return;

        $header = '<div class="icon32" id="'.$this->icon.'"><br/></div>';
        $full_title = __($this->title,WYSIJA);
        $action = $sub_title = '';

        if( isset($_REQUEST['action']) ){
            $action = $_REQUEST['action'];
        }

        if(isset($this->titlelink)){
            $my_title = '<a href="admin.php?page='.  esc_attr($_REQUEST['page']).'">'.$full_title.'</a> ';
        }else{
            $my_title = $full_title . ' ';
        }

        $header .= '<h2>'.$my_title.$sub_title.$this->menuTop($this->action,$data).'</h2>';
        $header .= $this->messages();

        echo $header;

    }


    /**
     * to help reproduce the standard view of wordpress admin view here is the footer part
     */
    function footer(){
        echo '</div>';/*end div class wrap*/
    }

    /**
     * to help reproduce the standard listing of a wordpress admin, here is the list of links appearing on top
     */
    function filtersLink(){
        if($this->statuses){
            ?>
            <div class="filter">
                <ul class="subsubsub">
                    <?php

                        $last_key = key(array_slice($this->statuses, -1, 1, TRUE));

                        foreach($this->statuses as $keyst=>$status){
                            $class='';
                            if(isset($_REQUEST['link_filter']) && $_REQUEST['link_filter'] === $status['key']) $class='current';

                            // set item count: defaults to 0
                            $count = (isset($status['count'])) ? (int)$status['count'] : 0;

                            if(isset($status['uri'])) {
                                echo '<li><a class="'.$class.'" href="'.$status['uri'].'">'.$status['title'].' <span class="count">('.$count.')</span></a>';
                            } else {
                                echo '<li><a class="'.$class.'" href="javascript:;">'.$status['title'].' <span class="count">('.$count.')</span></a>';
                            }
                            if($last_key!=$keyst) echo ' | ';
                            echo '</li>';

                        }
                    ?>
                </ul>
            </div>
            <?php
        }
    }

    /**
     * to help reproduce the standard listing of a wordpress admin, here is the search box
     */
    function searchBox(){
        $search="";
        if(isset($_REQUEST['search'])) $search =stripslashes($_REQUEST['search']);
        ?>
            <p class="search-box">
                <label for="wysija-search-input" class="screen-reader-text"><?php echo $this->search['title'] ?></label>
                <input type="text" value="<?php echo esc_attr($search) ?>" class="searchbox" name="search" id="wysija-search-input">
                <input type="submit" class="searchsub button" value="<?php echo esc_attr($this->search['title']) ?>">
            </p>
        <?php
    }

    /**
     * to help reproduce the standard listing of a wordpress admin, here is the table header/footer of the listing
     * @param type $data
     * @return type
     */
    function buildHeaderListing($data){
        //building the headers labels
        if(!$data) {
            //$this->notice(__("There is no data at the moment.",WYSIJA));
            return false;
        }
        $this->listingHeader='<tr class="thead">';
        $columns=$data[0];
        $sorting=array();
        //dbg($columns);
        foreach($columns as $row =>$colss){
            $sorting[$row]=" sortable desc";
            if(isset($_REQUEST["orderby"]) && $_REQUEST["orderby"]==$row) $sorting[$row]=" sorted ".$_REQUEST["ordert"];
        }

        $hiddenOrder="";
        if(isset($_REQUEST["orderby"])){
            $hiddenOrder='<input type="hidden" name="orderby" id="wysija-orderby" value="'.esc_attr($_REQUEST["orderby"]).'"/>';
            $hiddenOrder.='<input type="hidden" name="ordert" id="wysija-ordert" value="'.esc_attr($_REQUEST["ordert"]).'"/>';
        }
        $nk=false;
        if(isset($columns[$this->model->pk])){
            $nk=str_replace("_",'-',$this->model->pk);
            unset($columns[$this->model->pk]);
            $this->cols_nks[$this->model->pk]=$nk;
        }

        if($this->bulks){
            if($nk){
                $this->listingHeader='<th class="manage-column column-'.$nk.' check-column" id="'.$nk.'" scope="col"><input type="checkbox"></th>';
            }
        }

        foreach($columns as $key => $value){
            $nk=str_replace("_",'-',$key);
            $this->listingHeader.='<th class="manage-column column-'.$nk.$sorting[$key].'" id="'.$nk.'" scope="col">';

            if(isset($this->model->columns[$key]['label'])) $label=$this->model->columns[$key]['label'];
            else  $label=ucfirst($key);
            $this->listingHeader.='<a class="orderlink" href="#"><span>'.$label.'</span><span class="sorting-indicator"></span></a>';

            $this->listingHeader.='</th>';

            $this->cols_nks[$key]=$value;
        }
        $this->hiddenFields=$hiddenOrder;
        $this->listingHeader.='</tr>';
        return $this->listingHeader;
    }


    /**
     * to help reproduce the standard listing of a wordpress admin, here is the bulk action dropdown applied to selected rows
     */
    function globalActions($data=false,$second=false){
         ?>
        <div class="tablenav">
            <?php if($this->bulks){ ?>
            <div class="alignleft actions">
                <select name="action2" class="global-action">
                    <option selected="selected" value=""><?php echo esc_attr(__('Bulk Actions', WYSIJA)); ?></option>
                    <?php
                        foreach($this->bulks as $key=> $bulk){
                            echo '<option value="bulk_'.$key.'">'.$bulk['name'].'</option>';
                        }
                    ?>
                </select>
                <input type="submit" class="bulksubmit button-secondary action" name="doaction" value="<?php echo  esc_attr(__('Apply', WYSIJA)); ?>">
                <?php if(!$second)$this->secure('bulk_delete'); ?>
            </div>
            <?php } ?>
            <?php $this->pagination('',$second); ?>
            <div class="clear"></div>
        </div>
        <?php

    }

    // Pagination for listing pages.
    function pagination($paramsurl='', $second=false) {

        $number_of_pages = 1;
        $number_of_pages = ceil($this->model->countRows/$this->model->limit);

        echo '<div class="tablenav-pages">';

        $current_page = 1;
        if (isset($_REQUEST['pagi'])) {
            $current_page = (int)$_REQUEST['pagi'];
        }

        if ($number_of_pages > 1) {

            // Pagination input box to manually enter the page requested.
            $pagination_input = "";
            $suffix = "";
            if ($second) {
                $suffix = "-2";
            }
            $pagination_input = '<input id="wysija-pagination' . $suffix . '" type="text" name="pagi' . $suffix . '" size="4" value="' . $current_page . '" />';
            $pagination_input .= '<input id="wysija-pagination-max' . $suffix . '" type="hidden" name="pagimax' . $suffix . '" value="' . $number_of_pages . '" />';

            // Final pagination container.
            $pagi = '';

            // Pagination Previous Arrows.
            if ($current_page != 1) {
                $pagi .= '<a class="prev page-numbers" href="admin.php?page='.esc_attr($_REQUEST['page']).'&pagi=1'.$paramsurl.'" alt="1" title="'.sprintf(__('Page %1$s',WYSIJA),1).'">&laquo;</a>';
                if ($current_page>2) {
                    $pagi .= '<a class="prev page-numbers" href="admin.php?page='.esc_attr($_REQUEST['page']).'&pagi='.($current_page-1).$paramsurl.'" alt="'.($current_page-1).'" title="'.sprintf(__('Page %1$s',WYSIJA),($current_page-1)).'" >&lsaquo;</a>';
                }
            }

            // Input field and total pages.
            $pagi .= $pagination_input;
            $pagi.= '<span class="total-pages">' . sprintf(__('of %1$s',WYSIJA), $number_of_pages) . '</span>';

            // Pagination Next arrows.
            if($number_of_pages > 1 && $current_page != $number_of_pages){
                if (($number_of_pages - $current_page) >= 2) {
                    $pagi .= '<a class="next page-numbers" href="admin.php?page='.esc_attr($_REQUEST['page']).'&pagi='.($current_page+1).$paramsurl.'" alt="'.($current_page+1).'" title="'.sprintf(__('Page %1$s',WYSIJA),($current_page+1)).'">&rsaquo;</a>';
                }
                $pagi .= '<a class="next page-numbers" href="admin.php?page='.esc_attr($_REQUEST['page']).'&pagi='.$number_of_pages.$paramsurl.'" alt="'.$number_of_pages.'" title="'.sprintf(__('Page %1$s',WYSIJA),$number_of_pages).'" >&raquo;</a>';
            }

            echo $pagi;

        }

        echo '</div>';

    }



    /**
     * limit of records to show per page
     */
    function limitPerPage(){
        $limitPerpageS=array(5,10,20,50,100);
        if($this->model->countRows <= $limitPerpageS[0]) return true;
        $limitPerpage=array();

        foreach($limitPerpageS as $k => $count){
            $limitPerpage[] = $count;
            if($this->model->countRows < $count) break;
        }
        if(!$limitPerpage) return;

        $pagi='';
        if(isset($this->limit_pp)) $pagi.='<input id="wysija-pagelimit" type="hidden" name="limit_pp" value="'.$this->limit_pp.'" />';
        foreach($limitPerpage as $k => $count){
            $numperofpages=ceil($this->model->countRows/$count);
            $titleLink=' title="'.(isset($this->viewObj->title) ? $this->viewObj->title : sprintf(__('Split subscribers into %1$s pages.',WYSIJA),$numperofpages)).'" ';
            /*if($urlbase)    $linkk=$urlbase.'&limit_pp='.$count;
            else    $linkk='admin.php?page='.$_REQUEST['page'].'&limit_pp='.$count;*/
            $linkk='javascript:;';
            if(isset($_REQUEST['limit_pp'])){
                if($_REQUEST['limit_pp']==$count) $pagi.='<span '.$titleLink.'  class="page-limit current">'.$count.'</span>';
                else $pagi.='<a href="'.$linkk.'" '.$titleLink.' class="page-limit" >'.$count.'</a>';
            }else{

                if($this->model->limit==$count) $pagi.='<span class="page-limit current" '.$titleLink.' >'.$count.'</span>';
                else $pagi.='<a href="'.$linkk.'" '.$titleLink.' class="page-limit" >'.$count.'</a>';
            }
            if($k+1 < count($limitPerpage)) $pagi.=" | ";
        }
        ?>
        <div class="tablenav-limits subsubsub">
            <span class="displaying-limits"><?php
            if(isset($this->viewObj->msgPerPage)){
                echo $this->viewObj->msgPerPage;
            }else{
                _e('Subscribers to show per page:',WYSIJA);
            }
             ?></span>
            <?php
                echo $pagi;
            ?>
        </div>
        <?php


    }


    /**
     * here is a helper for each column value on a listing view
     * @param type $key
     * @param type $val
     * @param type $type
     * @return type
     */
    function fieldListHTML($key,$val,$params=array()){
        /*get the params of that field if there is*/
        $field_type = (isset($params['type']) ? $params['type'] : '');

        switch($field_type) {
            case 'pk':
                return '<th class="check-column" scope="col"><input class="checkboxselec" type="checkbox" value="'.$val.'" id="'.$key.'_'.$val.'" name="wysija['.$this->model->table_name.']['.$key.'][]"></th>';
                break;
            case 'boolean':

                $wrap='<td class="'.$key.' column-'.$key.'">';
                $wrap.=$params['values'][$val];
                $wrap.='</td>';

                break;
            case 'date':

                $wrap='<td class="'.$key.' column-'.$key.'">';
                $wrap.=$this->fieldListHTML_created_at($val);
                $wrap.='</td>';

                break;
            case 'time':

                if(!isset($params['format']))$params['format']='';

                $wrap='<td class="'.$key.' column-'.$key.'">';
                $wrap.=$this->fieldListHTML_created_at($val,$params['format']);
                $wrap.='</td>';

                break;
            default:
                $wrap='<td class="column-'.$key.'">';
                $specialMethod="fieldListHTML_".$key;
                if(method_exists($this, $specialMethod)) $wrap.=$this->$specialMethod($val);
                else $wrap.=$val;

                $wrap.=$this->getActionLinksList($key);

                $wrap.='</td>';

        }
         return $wrap;
    }

    /**
     * this function adds a list of action links under the column valued in a listing
     * @param type $column
     * @return string
     */
    function getActionLinksList($column,$manual=false){
        $wrap='';
        if($this->column_action_list==$column ||$manual){
            $wrap='<div class="row-actions">';
            end($this->column_actions);
            $lastkey=key($this->column_actions);
            reset($this->column_actions);
            foreach($this->column_actions as $action => $title){
                switch($action){
                    case "delete":
                        $noncefield='&_wpnonce='.$this->secure(array('action'=>$action,'id'=>$this->valPk),true);
                        break;
                    default:
                        $noncefield="";
                }
                $separator='';

                if($action!=$lastkey)   $separator=' | ';

                if(!isset($this->model->model_name)) $this->model->model_name=$this->model->table_name;
                if(!isset($this->model->model_prefix)) $this->model->model_prefix=$this->model->table_prefix;
                $wrap.='<span class="'.$action.'">
                    <a href="admin.php?page='.$this->model->model_prefix.'_'.$this->model->model_name.'&id='.$this->valPk.'&action='.$action.$noncefield.'" class="submit'.$action.'">'.$title.'</a>'.$separator.'
                </span>';

            }

            $wrap.='</div>';
        }
        return $wrap;
    }


    /**
     * this function is here to help in generic forms
     * @param type $key
     * @param type $val
     * @param type $type
     * @return type
     */
    function fieldFormHTML($key,$wrapped){
        if(isset($this->model->columns[$key]['label'])) $label=$this->model->columns[$key]['label'];
        else  $label=ucfirst($key);
        $desc='';
        if(isset($this->model->columns[$key]['desc'])) $desc='<p class="description">'.$this->model->columns[$key]['desc'].'</p>';
        $wrap='<th scope="row">
                    <label for="'.$key.'">'.$label.$desc.' </label>
                </th><td>';

        $wrap.=$wrapped;
        $wrap.='</td>';
        return $wrap;
    }

    function buildMyForm($form_fields,$data,$model,$required=false){

        $form_html = '';
        foreach($form_fields as $form_key =>$col_params){
            $class = $value = '';
            $params_column = false;
            if(isset($col_params['rowclass'])) $class = ' class="'.$col_params['rowclass'].'" ';
            if(isset($col_params['row_id'])) $class.= ' id="'.$col_params['row_id'].'" ';
            $form_html .= '<tr '.$class.'>';
            if($model == 'config'){
                $value = $this->model->getValue($form_key);
            }else{
                if($form_key != 'lists')   {
                    if(is_array($model)){
                        foreach($model as $mod){
                            if(isset($data[$mod][$form_key])){
                                $value=$data[$mod][$form_key];
                                break;
                            }
                        }
                    }else{
                        if(isset($col_params['isparams'])){
                            $params=$data[$model][$col_params['isparams']];

                            $value='';
                            if(isset($params[$form_key]))    $value=$params[$form_key];
                            $params_column=$col_params['isparams'];
                        }
                        if(isset($data[$model][$form_key])) $value=$data[$model][$form_key];
                    }
                    if($value) $value;
                    elseif(isset($_REQUEST['wysija'][$this->model->table_name][$form_key])) $value=$_REQUEST['wysija'][$this->model->table_name][$form_key];
                    elseif(isset($col_params['default'])) $value=$col_params['default'];
                    else $value='';
                }elseif(isset($data[$form_key])) $value=$data[$form_key];
                elseif(isset($col_params['default'])) $value=$col_params['default'];
                else $value='';
            }

            if($required && !isset($col_params['class']))   $col_params['class']=$this->getClassValidate($this->model->columns[$form_key],true);

            if(isset($col_params['label'])) $label=$col_params['label'];
            else  $label=ucfirst($form_key);
            $desc='';
            if(isset($col_params['desc'])){
                if(isset($col_params['link'])) $col_params['desc']=str_replace(array('[link]','[/link]'),array($col_params['link'],'</a>'),$col_params['desc']);
                $desc='<p class="description">'.$col_params['desc'].'</p>';
            }

            $colspan=' colspan="2" ';
            if(!isset($col_params['1col'])){
                $form_html.='<th scope="row">';
                if(!isset($col_params['labeloff'])) $form_html.='<label for="'.$form_key.'">';
                $form_html.=$label.$desc;
                if(!isset($col_params['labeloff']))  $form_html.=' </label>';
                $form_html.='</th>';
                $colspan='';
            }
            $form_html.='<td '.$colspan.'>';
            $form_html.=$this->fieldHTML($form_key,$value,$model,$col_params,$params_column);
            $form_html.='</td>';

            $form_html.='</tr>';
        }
        return $form_html;
    }
    /**
     *
     * @param type $key
     * @param type $val
     * @param type $type
     * @return type
     */
    function fieldHTML($key,$val='',$model='',$params=array(),$paramscolumn=false){
        $classValidate=$wrap='';
        // get the params of that field if there is
        $type=$params['type'];
        // js validator class setup
        if($params)  $classValidate=$this->getClassValidate($params);

        if($paramscolumn){
            $col=$paramscolumn.']['.$key;
        }else $col=$key;

        $id_field=$key;
        if(isset($params['id'])){
            $id_field=$params['id'];
        }
        $field_name='wysija['.$model.']['.$col.']';
        $helper_forms=WYSIJA::get('forms','helper');
        switch($type){
            case 'pk':
                return '<input type="hidden" value="'.$val.'" id="'.$id_field.'" name="'.$field_name.'">';
                break;
            case 'boolean':

                $wrap.=$helper_forms->dropdown(array('id'=>$id_field, 'name'=>$field_name),$params['values'],$val,$classValidate);
                break;
            case 'roles':
                $wptools=WYSIJA::get('wp_tools','helper');
                $editable_roles=$wptools->wp_get_editable_roles();

                $wrap.=$helper_forms->dropdown(array('id'=>$id_field, 'name'=>$field_name),$editable_roles,$val,$classValidate);
                break;
            case 'password':
                if(!isset($params['size'])){
                    $classValidate.=' size="80"';
                }else {
                    $classValidate.=' size="'.$params['size'].'"';
                }

                $wrap.=$helper_forms->input(array('type'=>'password','id'=>$id_field, 'name'=>$field_name),$val,$classValidate);
                break;
            case 'disabled_radio':

                $wrap.=$helper_forms->radios(array('id'=>$id_field, 'name'=>$field_name, 'disabled'=>'disabled'),$params['values'],$val,$classValidate);
                break;
            case 'radio':

                $wrap.=$helper_forms->radios(array('id'=>$id_field, 'name'=>$field_name),$params['values'],$val,$classValidate);
                break;
            case 'dropdown_keyval':
                $newoption=array();
                foreach($params['values'] as $vall){
                    $newoption[$vall]=$vall;
                }

                $wrap.=$helper_forms->dropdown(array('id'=>$id_field, 'name'=>$field_name),$newoption,$val,$classValidate);
                break;
            case 'dropdown':

                $wrap.=$helper_forms->dropdown(array('id'=>$id_field, 'name'=>$field_name),$params['values'],$val,$classValidate);
                break;
            case 'wysija_pages_list':
                $wrapd=get_pages( array('post_type'=>"wysijap",'echo'=>0,'name'=>$field_name,'id'=>$id_field,'selected' => $val,'class'=>$classValidate) );

                break;
            case 'pages_list':
                $wrap.=wp_dropdown_pages( array('echo'=>0,'name'=>$field_name,'id'=>$id_field,'selected' => $val,'class'=>$classValidate) );
                break;
            default:
                if(!isset($params['size'])){
                    $classValidate.=' size="80"';
                }else{
                    $classValidate.=' size="'.$params['size'].'"';
                }
                if(isset($params['class'])){
                    $classValidate.=' class="'.$params['class'].'"';
                }

                $specialMethod='fieldFormHTML_'.$type;

                if(method_exists($this, $specialMethod)) $wrap.=$this->$specialMethod($key,$val,$model,$params);
                else{

                    if(method_exists($helper_forms, $type)){
                        $dataInput=array('id'=>$id_field, 'name'=>$field_name);
                        if(isset($params['cols']))$dataInput['cols']=$params['cols'];
                        if(isset($params['rows']))$dataInput['rows']=$params['rows'];
                        $wrap.=$helper_forms->$type($dataInput,$val,$classValidate);
                    }else{
                        $wrap.=$helper_forms->input(array('id'=>$id_field, 'name'=>$field_name),$val,$classValidate);
                    }
                }
        }
        return $wrap;
    }

    /**
     * this function is the default listing function
     * @param type $data
     */
    function listing($data,$simple=false){

        if(!$simple)    $this->globalActions();

            $html='<table cellspacing="0" class="widefat fixed">
                <thead>';
            $html.=$this->buildHeaderListing($data);
            $html.='</thead>';

             $html.='<tfoot>';
             $html.=$this->listingHeader;
             $html.='</tfoot>';

             $html.='<tbody class="list:'.$this->model->table_name.' '.$this->model->table_name.'-list" id="wysija-'.$this->model->table_name.'" >';

                        $listingRows='';
                        $alt=true;
                        foreach($data as $row =>$columns){
                            $classRow='';
                            if($alt) $classRow=' class="alternate" ';
                             $listingRows.='<tr'.$classRow.' id="'.$this->model->table_name.'-'.$this->model->table_name.'">';
                             $valpkcol=false;
                             if(isset($columns[$this->model->pk])) {
                                $this->valPk=$columns[$this->model->pk];
                                $valpkcol=$this->model->columns[$this->model->pk];
                                $this->model->columns[$this->model->pk]['type']='pk';
                                unset($columns[$this->model->pk]);
                            }
                            if($this->bulks){

                                if($valpkcol){
                                    $listingRows.=$this->fieldListHTML($this->model->pk,$this->valPk,$this->model->columns[$this->model->pk]);
                                }
                            }



                            foreach($columns as $key => $value){
                                if(isset($this->model->columns[$key])) $val=$this->model->columns[$key];
                                else $val='';
                                $listingRows.=$this->fieldListHTML($key,$value,$val);
                                $listingRows.='</th>';
                            }
                            $alt=!$alt;
                        }
                        $html.= $listingRows;



           $html.=' </tbody>
            </table>';
           if(!$simple) echo $html;
            if(!$simple) {
                $this->globalActions(false,true);
                $this->limitPerPage();
                echo $this->hiddenFields;
            }

            if($simple) return $html;
    }

    /**
     * here is a generic form view
     * @param type $data
     */
    function edit($data){

        $formid='wysija-'.$_REQUEST['action'];
        if($_REQUEST['action']=="edit"){
            $buttonName=__('Modify',WYSIJA);
        }else{
            $buttonName=__('Add',WYSIJA);
        }

        ?>

        <form name="<?php echo $formid ?>" method="post" id="<?php echo $formid ?>" action="" class="form-valid">

            <table class="form-table">
                <tbody>
                    <?php
                    foreach($data as $row =>$columns){
                        $formFields='<tr>';
                        if(isset($columns[$this->model->pk])){
                            $this->valPk=$columns[$this->model->pk];
                            $this->model->columns[$this->model->pk]['type']="pk";
                            $formFields.=$this->fieldHTML($this->model->pk,$this->valPk,$this->model->table_name,$this->model->columns[$this->model->pk ]);
                            unset($columns[$this->model->pk]);
                        }
                        foreach($columns as $key => $value){
                            $formFields.=$this->fieldFormHTML($key,$this->fieldHTML($key,$value,$this->model->table_name,$this->model->columns[$key]));
                            $formFields.='</tr>';
                        }
                        echo $formFields;
                    }
                    ?>
                </tbody>
            </table>
            <p class="submit">
                <?php $this->secure(array('action'=>"save", 'id'=> $this->valPk)); ?>
                <input type="hidden" value="save" name="action" />
                <input type="submit" value="<?php echo esc_attr($buttonName); ?>" class="button-primary wysija">
            </p>
        </form>
        <?php
    }

     /**
     * here is a generic form view
     * @param type $data
     */
    function view($data,$echo=true){

           $html=' <table class="form-table">
                <tbody>';
                    foreach($data as $row =>$columns){
                        $formFields='<tr>';

                        foreach($columns as $key => $value){
                            $formFields.=$this->fieldFormHTML($key,$value);
                            $formFields.='</tr>';
                        }
                        $html.= $formFields;
                    }

             $html.='   </tbody>
            </table>';

          if($echo) echo $html;
          else return $html;
    }

    function fieldFormHTML_fromname($key,$val,$model,$params){
        $formObj=WYSIJA::get('forms','helper');
        $disableEmail=false;
        if($model!='config') $model='email';
        if($key=='from_name')   $keyemail='from_email';
        else    $keyemail='replyto_email';

        //$dataInputEmail=array('class'=>'validate[required]', 'id'=>$keyemail,'name'=>"wysija[$model][$keyemail]", 'size'=>40);
        $dataInputEmail=array('class'=>'validate[required,custom[email]]', 'id'=>$keyemail,'name'=>"wysija[$model][$keyemail]", 'size'=>40);

        if(isset($this->data['email'][$key])){
            $valname=$this->data['email'][$key];
            $valemail=$this->data['email'][$keyemail];
        }else{
            $valname=$this->model->getValue($key);
            $valemail=$this->model->getValue($keyemail);
        }

        //if from email and sending method is gmail then the email is blocked to the smtp_login
        if($key=='from_name'){
            $modelConfig=WYSIJA::get('config','model');
            switch($modelConfig->getValue('sending_method')){
                case 'gmail':
                    $dataInputEmail['readonly']='readonly';
                    $dataInputEmail['class']='disabled';
                    $valemail=$modelConfig->getValue('smtp_login');

                    //if the user didn't enter the email address then we put it ourself
                    if(strpos($valemail, '@')===false && strpos($valemail, '@gmail.com')===false){
                        $valemail.='@gmail.com';
                    }
                    $from_name_field=$formObj->input($dataInputEmail ,$valemail);
                    break;
                case 'network':
                    $from_name_field='&nbsp;&nbsp;'.$modelConfig->getValue('ms_from_email').$formObj->hidden($dataInputEmail ,$valemail);
                    break;
                default :
                    $from_name_field=$formObj->input($dataInputEmail ,$valemail);
            }

        }else{
            $from_name_field=$formObj->input($dataInputEmail ,$valemail);
        }


        $fieldHtml=$formObj->input( array('class'=>'validate[required]', 'id'=>$key,'name'=>"wysija[$model][$key]"),$valname);
        $fieldHtml.=$from_name_field;
        return $fieldHtml;
    }

    function _savebuttonsecure($data,$action="save",$button=false,$warning=false){
        if(!$button) $button=__("Save",WYSIJA);
        ?>
            <p class="submit">
                <?php

                $secure=array('action'=>$action);
                if(isset($data[$this->model->table_name][$this->model->pk]))    $secure["id"]=$data[$this->model->table_name][$this->model->pk];
                $this->secure($secure); ?>
                <input type="hidden" name="wysija[<?php echo $this->model->table_name ?>][<?php echo $this->model->pk ?>]" id="<?php echo $this->model->pk ?>" value="<?php if(isset($data[$this->model->table_name][$this->model->pk])) echo esc_attr($data[$this->model->table_name][$this->model->pk]) ?>" />
                <input type="hidden" value="<?php echo $action ?>" name="action" />
                <input type="submit" id="next-steptmpl" value="<?php echo esc_attr($button) ?>" name="submit-draft" class="button-primary wysija"/>
                <?php if($warning)  echo $warning; ?>
            </p>
        <?php
    }

}
