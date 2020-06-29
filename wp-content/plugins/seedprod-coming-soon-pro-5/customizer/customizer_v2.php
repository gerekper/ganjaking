<?php
if(seed_cspv5_cu('none')){
    return '';
}

wp_enqueue_media();

$localhost = array('127.0.0.1','::1');

$is_localhost = false;
if(in_array($_SERVER['REMOTE_ADDR'], $localhost) || !empty($_GET['debug'])){
    $is_localhost = true;
}

?>

<?php if( $is_localhost){ ?>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js"></script>
    <script src="https://unpkg.com/vee-validate@latest"></script>
    <script src="//cdn.jsdelivr.net/npm/sortablejs@1.7.0/Sortable.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/2.15.0/vuedraggable.min.js"></script>
<?php }else{ ?>
    <script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/vue.min.js"></script> 
<?php } ?>

<!-- css -->
<link href="<?php echo SEED_CSPV5_PLUGIN_URL ?>template/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.12/css/all.css" integrity="sha384-G0fIWCsCzJIMAVNQPfjH08cyYaUtMwjJwqiRKxxE/rx96Uroj1BtIQ6MLJuheaO9" crossorigin="anonymous">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.0/normalize.min.css" /> -->


<!-- Plugins -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.css">

<!-- Editor css -->
<link href="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/css/editor-style_v2.css" rel="stylesheet">

<div id="seed-cspv5-customizer-wrapper">

    <div id="seed-cspv5-sidebar-wrapper" v-cloak>
        <div id="seed-cspv5-sidebar-actions">
            <button class="button-primary" v-on:click="save_page" :disabled="page_meta.doing_ajax == 1 ? true : false">{{page_meta.save_btn_txt}}</button>
        </div>

        <div id="seed-cspv5-sidebar-form">
            <div id="page_name" class="seed-cspv5-form-field form-group">
                <label for="name">Name<span class="control-label required">*</span></label>
                
                <input autofocus v-validate="'required'" :class="{'input': true, 'seed-cspv5-is-danger': errors.has('name') }"  name="name" v-model="page.name" class="form-control input-sm">
                <span v-show="errors.has('name')" class="help seed-cspv5-is-danger">{{ errors.first('name') }}</span>
                <small>Name of the product (for default display and management purposes).</small> 
                
            </div> 
            
            <div class="seed-cspv5-form-field form-group">
                <label for="logo" class="control-label required">Logo</label>
                <input name="logo" v-model="settings.logo" class="form-control input-sm">
                <input class="button-secondary" type='button' value="Select Image" v-on:click="insert_logo" />
                <div class="img-preview" v-if="page.logo_preview">
                        <img v-bind:src="page.logo_preview">
                        <i class="fas fa-times" v-on:click="remove_logo"></i>
                </div>
            </div>

            <div class="seed-cspv5-form-field">
                <label for="name">Headline</label>
                <input v-model="settings.headline" class="regular-text" v-on:input="update_headline"> 
            </div>

            <div class="seed-cspv5-form-field">
                <label for="name">Description</label>
                <textarea v-model="settings.description" style="display:none" ></textarea>
                <?php
                $content   = $settings->description;
                $editor_id = 'description';
                $args      = array(
                    'textarea_name' => "description",
                    'editor_height' => "200px"
                ); 
                
                wp_editor( $content, $editor_id, $args ); 
                ?>
            </div>

            <div class="seed-cspv5-form-field">
                <label for="name">Sections</label>
                <draggable :list="settings.blocks" class="drag_area" :options="{handle:'.fa-list'}"> 
                    <div v-for="(block, index) in settings.blocks">
                          <div style="padding:0 3px 10px 0">
                          <i class="fas fa-list"></i> {{block}}
                          </div>
                    </div>
                </draggable>
            </div>

        </div>

    <div id="dragbar"></div>
    </div><!-- /#seed-cspv5-sidebar-wrapper -->

    <div id="seed-cspv5-preview-wrapper">
                    <div id="seed-cspv5-ajax-status"><img src="<?php echo admin_url() ?>/images/spinner.gif"></div>
        
                    <iframe id="seed-cspv5-preview" src="<?php echo home_url('/','relative').'?seed_cspv5_preview='. $page_id?>" ></iframe>  
    </div><!-- /#seed-cspv5-preview-wrapper -->

</div> <!-- /#seed-cspv5-customizer-wrapper -->


<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.js" type="text/javascript"></script>


<!-- App JS -->

<script>
    Vue.use(VeeValidate);

    <?php $save_page_ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_save_page_v2','seed_cspv5_save_page_v2')); ?>
    var seed_cspv5_save_url = "<?php echo $save_page_ajax_url; ?>";

    var seed_cspv5_app = new Vue({
        el: '#seed-cspv5-customizer-wrapper',
        data: <?php echo json_encode(array('page_meta'=>array('logo_preview'=>'','doing_ajax'=>0,'save_btn_txt'=>'Save'),'page'=>$page, 'settings'=> $settings)); ?>,
        methods: {
            insert_logo: function(){
                wp.media.editor.send.attachment = function(props, attachment){
                    seed_cspv5_app.settings.logo = attachment.url;
                    seed_cspv5_app.page.logo_preview = attachment.url;
                    jQuery('#seed-cspv5-preview').contents().find('#cspio-logo').prop('src', attachment.url);

                }

                wp.media.editor.open();					
            },
            remove_logo: function(){
                jQuery('#seed-cspv5-preview').contents().find('#cspio-logo').prop('src', '');
                seed_cspv5_app.settings.logo = '';
                seed_cspv5_app.page.logo_preview = '';			
            },
            update_headline: function(){
                jQuery('#seed-cspv5-preview').contents().find('#cspio-headline').html(this.settings.headline);
            },
            save_page: function () {
                $valid = this.$validator.validateAll().then((result) => {
                    if (result) {

                    // Submit data
                    jQuery.ajax({
                        type: "POST",
                        url : seed_cspv5_save_url,
                        data : this.$data,
                        beforeSend : function(){
                            seed_cspv5_app.page_meta.doing_ajax = 1;
                            seed_cspv5_app.page_meta.save_btn_txt = 'Saving ...';
                        },
                        success : function(data){
                            document.getElementById('seed-cspv5-preview').contentWindow.location.reload(true);
                            seed_cspv5_app.page_meta.doing_ajax = 0;
                            seed_cspv5_app.page_meta.save_btn_txt = 'Save';
                            toastr.success('Saved!');
                            
                        },
                        error: function(xhr){
                            seed_cspv5_app.page_meta.doing_ajax = 0;
                            seed_cspv5_app.page_meta.save_btn_txt = 'Save';
                            if(xhr.status == '400'){
                                toastr.error('The form is invalid.');
                            }else{
                                toastr.error('Could not be saved. Refresh the page and try again. Please contact Support if you continue to experience this issue.');
                            }
                        }
                    });
                    return
                }
                toastr.error('The form is invalid.');
            });

            }
        },
        components: {
        },
        computed: {

        }
    });

    // Listen for WP Eitor Changes
    jQuery( document ).on( 'tinymce-editor-init', function( event, editor ) {
        if(editor.id == 'description'){
            var description = tinyMCE.get('description');
            description.on('keyup',function(ed) {
                var contents = tinymce.get('description').getContent();
                jQuery('#seed-cspv5-preview').contents().find('#cspio-description').html(contents); 
                seed_cspv5_app.settings.description = contents;
            });
            description.on('paste',function(ed) {
                var contents = tinymce.get('description').getContent();
                jQuery('#seed-cspv5-preview').contents().find('#cspio-description').html(contents); 
                seed_cspv5_app.settings.description = contents;
            });
            description.on('blur',function(ed) {
                var contents = tinymce.get('description').getContent();
                jQuery('#seed-cspv5-preview').contents().find('#cspio-description').html(contents); 
                seed_cspv5_app.settings.description = contents;
            });
        }
        
    });

    jQuery( "#description" ).on('input',function(e){
        seed_cspv5_app.settings.description = jQuery(this).val();
    });

    // listeners
    jQuery('#seed-cspv5-preview').load(function(){

        var iframe = $('#seed-cspv5-preview').contents();

        iframe.find("#cspio-headline").click(function(){
            seed_cspv5_scroll_to_el('#page_name');
        });
    });

    function seed_cspv5_scroll_to_el(el){
        jQuery('#seed-cspv5-sidebar-wrapper').scrollTop(jQuery('#seed-cspv5-sidebar-wrapper').scrollTop() + jQuery('#page_name').position().top);
    }

    


    // Sidebar drag
    var i = 0;
    jQuery('#dragbar').mousedown(function(e) {

    e.preventDefault();
    jQuery("#seed-cspv5-preview").hide();
    jQuery(window.top).mousemove(function(e) {
        if(e.pageX > 300){    
        jQuery('#seed-cspv5-sidebar-wrapper').css("width", e.pageX + 2);
        jQuery('.page-container').css("padding-left", e.pageX + 2);
        }
    });
    });

    jQuery(window.top).mouseup(function(e) {
        jQuery(window.top).unbind('mousemove');
        jQuery("#seed-cspv5-preview").show();
    });
</script>




