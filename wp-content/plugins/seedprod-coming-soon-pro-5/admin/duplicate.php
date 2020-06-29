<!-- Main Content -->
<div class="wrap seed_cspv5 " >
<?php include(SEED_CSPV5_PLUGIN_PATH.'admin/header.php') ?>

<h1>Enter the Page's Path</h1>
<form id="seed_cspv5_duplicate_form">
<input type="hidden" name="page_id" value="<?php echo $_REQUEST['id'] ?>">
<?php echo home_url() ?>/<input name="path" type="text" id="path" value="" class="regular-text" placeholder="" >
<button id="seed_cspv5_duplicate_form_button" class="button-primary">Duplicate Page</button>
</form>

</div>

<script>
	<?php $duplicate_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_duplicate_page','seed_cspv5_duplicate_page')); ?>
	var duplicate_url = "<?php echo $duplicate_url; ?>";

	jQuery( "#seed_cspv5_duplicate_form_button" ).click(function(e) {
		e.preventDefault();
		var dataString = jQuery( '#seed_cspv5_duplicate_form' ).serialize();
	    

		jQuery.ajax({
	        type: "POST",
	        url : duplicate_url,
	        data : dataString,
	        beforeSend : function(data){
	                    jQuery('#seed_cspv5_duplicate_form_button').prop("disabled",true).html('Duplicating...');
	        },
	        success : function(data){
	        	console.log(data);
	          location.href = '<?php echo admin_url() ?>options-general.php?page=seed_cspv5_customizer&seed_cspv5_customize='+data;
	        }
	    });
    });

</script>