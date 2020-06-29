<style>
.grid-item {
  margin-bottom: 0px;
  float:left;
  height:275px;
  width:300px;
  overflow: hidden;
  margin-right: 5px;
}

.grid-item p{
  margin:0;
}
#wpfooter{
  display:none;
}

.masonry p{
  margin-top:0px;
  margin-bottom:5px;
}
</style>
<!-- Main Content -->
<div class="wrap seed_cspv5 " >
<?php include(SEED_CSPV5_PLUGIN_PATH.'admin/header.php') ?>

<?php if(isset($_GET['type']) && $_GET['type'] == 'lp'){ ?>
<h1>Enter the Page's Name</h1>
<input name="page_name" type="text" id="page_name" value="" class="regular-text" placeholder="Page Name" >
<h1>Select a Theme</h1>
<?php } ?>
<div class="media-frame wp-core-ui mode-grid mode-edit hide-menu"> 
<h1>Themes</h1>
<?php echo $themes; ?>
</div>
</div>

<script>
<?php $return_url = (isset($_GET['return']))? preg_replace("/#.*|#.*&|&tab=design/", "", $_GET['return']).'&tab=design' : ''; ?>
var return_url = '<?php echo urldecode($return_url); ?>';
<?php $page_id = (isset($_GET['page_id']))? $_GET['page_id'] : '-1'; ?>
var page_id = '<?php echo $page_id; ?>';

<?php $type = (isset($_GET['type']))? $_GET['type'] : ''; ?>
var type = '<?php echo $type; ?>';

<?php $path = (isset($_GET['path']))? $_GET['path'] : ''; ?>
var path = '<?php echo $path; ?>';

<?php $name = (isset($_GET['name']))? $_GET['name'] : ''; ?>
var name = '<?php echo $name; ?>';

<?php $load_theme_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_load_theme','seed_cspv5_load_theme')); ?>
var load_theme_url = "<?php echo $load_theme_url; ?>";

jQuery( "#cancel-btn" ).click(function(e) {
	e.preventDefault();
	var return_url = '{{$return}}';
	if(return_url != ''){
		window.location.href = return_url;
	}
	
});

jQuery( ".grid-item a,.seed_cspv5_no_themes" ).on('click',function(e) {
    e.preventDefault();
    theme = jQuery(this).attr('href');
    if(jQuery('#page_name').val() != undefined){
      name = jQuery('#page_name').val();
    }
    jQuery( this ).parent().prepend( '<div class="seed_cspv5_loading">Loading Theme <img src="<?php echo admin_url() ?>/images/spinner.gif"></div>' );
    //console.log(theme);
    jQuery.get( load_theme_url+theme.replace('?','&')+'&page_id='+page_id+'&type='+type+'&path='+path+'&name='+name, function( data ) {
      //console.log(data);
      if(data != 'false'){
        if(return_url != ''){
          location.href = return_url;
        }else{
          location.href = '<?php echo admin_url() ?>options-general.php?page=seed_cspv5_customizer&seed_cspv5_customize='+data;
        }
      }else{
        alert('Error Loadding Theme. Please Try Again.');
      }
    }).fail(function() {
      alert('Error Loadding Theme. Please Try Again.');
    }).always(function() {
      jQuery( ".seed_cspv5_loading" ).remove();
    });
    
});


// jQuery( document ).ready(function() {
// jQuery(window).load(function() {
// imagesLoaded( document.querySelector('.grid'), function( instance ) {
// var elem = document.querySelector('.grid');
// var msnry = new Masonry( elem, {
//   // options
//   itemSelector: '.grid-item',
//   columnWidth: 300,
//   gutter: 6
// });
// });
// }); 
// });

</script>
<div style="clear:both"></div>
