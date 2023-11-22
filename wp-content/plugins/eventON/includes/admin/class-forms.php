<?php
/** 
 * EventON Admin forms interface
 * A global forms object that will be used by all eventon products
 * only Admin 
 */

class EVO_Forms{

	function get_view($fields, $values= array()){

		ob_start();
		echo "<div class='evo_admin_form'>";

		foreach($fields as $key=>$data){

			$v = isset($values[$key])? $values[$key]: '';
			$rq = isset($data['rq']) && $data['rq']? true: false;
			$F = isset($data['F'])? $data['F']: $key;

			switch($key){
				case 'plain':	?><<?php echo $data['markup'];?>><?php echo $data['name'];?></<?php echo $data['markup'];?>><?php	break;
				case 'hidden':
					?><input class='evo_admin_field ' type='hidden' name='<?php echo $F;?>' value='<?php echo $v;?>'/><?php
				break;
				case 'input_base':
					?><p class='<?php echo $key;?>'>
						<input class='evo_admin_field ' type='text' name='<?php echo $F;?>' value='<?php echo $v;?>'/>
					</p><?php
				break;
				case 'input_2':
					?><p class='<?php echo $key;?>'>
					<span class='t'><?php echo $data['name'];?>: <?php echo $rq?'*':'';?></span>
					<input type='text' class='evo_admin_field <?php echo $rq?'rq':'';?>' name='<?php echo $F;?>' value='<?php echo $v;?>'/><?php

					if(isset($data['description'])) echo "<em>" . $data['description'] . "</em>";
					echo "</p>";
				break;

				case 'textarea':
					?><p class='<?php echo $key;?>'>
					<span class='t'><?php echo $data['name'];?>: <?php echo $rq?'*':'';?></span>
					<textarea class='evo_admin_field <?php echo $rq?'rq':'';?>' name='<?php echo $F;?>'><?php echo $v;?></textarea><?php

					if(isset($data['description'])) echo "<em>" . $data['description'] . "</em>";

					echo "</p>";
				break;
				case 'submit':
					$attrs = '';
					if(isset($data['attrs'])){
						foreach($data['attrs'] as $k=>$kk){
							$attrs .= $k .'="'. $kk .'" ';
						}
					}

					$cancel = '';
					if(isset($data['cancel'])){
						$cancel = " <a class='".$data['cancel_class']."'>". $data['cancel_name'] ."</a>";
					}

					?><p class='<?php echo $key;?>'><a class='<?php echo $data['class'];?>' <?php echo $attrs;?>><?php echo $data['name'];?></a><?php echo $cancel;?></p><?php
				break;
			}
		}
		
		echo "</div>";

		return ob_get_clean();

	}
}