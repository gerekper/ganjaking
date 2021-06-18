<?php
	if( !isset( $updb_customizer_api ) ){
		$updb_customizer_api = new UPDBProfileCustomizer();
	}
	if( !isset( $updb_default_options ) ){
		$updb_default_options = new UPDBDefaultOptions();
	}
	$no_of_col = $updb_default_options->updb_get_option( 'number_of_column' );
	$col_width = 96/$no_of_col."%";
	$view_fields = userpro_fields_group_by_template( 'view', 'default' );
	do_action( 'updb_before_widget_container', $view_fields );
?>
<div class="widget-container">
	<?php
		for( $j=1;$j<=$no_of_col;$j++ ){
	?>
		<div class="col-widget col-widget_<?php echo $j;?>" style="width:<?php echo $col_width?>">
		 <ul>
			<?php
				$updb_customizer_api->show_column_widgets( $user_id, $j, $args, $view_fields , $i );
			?>
		 </ul>
		</div>
	<?php
		}
		do_action( "updb_after_column_$j",  $user_id, $args, $view_fields );	
	?>
</div>
<?php
	do_action( 'updb_after_widget_container', $view_fields );
?>
