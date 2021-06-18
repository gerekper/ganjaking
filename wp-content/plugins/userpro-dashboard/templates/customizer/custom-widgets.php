<div class="updb-widget-style">
	<div class="updb-view-custom-widget">
		<div class="updb-basic-info">
			<?php echo $updb_available_widgets[$col_widget]['title'];?>	
		</div>
		<div class="updb-custom-content">
			<?php 
                            $allContent = $updb_available_widgets[$col_widget]['widget_content']; 
                            $textContent = "";
                            preg_match_all("/\[[^\]]*\]/", $allContent, $matches);
                            for($i=0;$i<count($matches);$i++)
                            {
                                /***** This code is commented because the media manager related widgets are created in new update *****/
//                                if(preg_match('#media\s*\=\s*view#ims',$matches[0][$i],$match)){
//                                    $user_data = ' user_id='.$user_id.']';
//                                    $matches[0][$i] = preg_replace('#\]#ims',$user_data , $matches[0][$i]);
//                                }
                                if(!empty($matches[0])){
                                    $updb_doshortcode = do_shortcode($matches[0][$i]);
                                }
                                else{
                                    $updb_doshortcode = $allContent;
                                }
                                $textContent .= str_replace($matches[$i],$updb_doshortcode,$allContent);
                            }
                            echo stripslashes($textContent);
			?>
		</div>
	</div>
</div>
