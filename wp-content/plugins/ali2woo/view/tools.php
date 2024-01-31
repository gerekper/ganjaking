<div class="a2w-content">
    
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div style="padding:20px 0"><h2>Attention! Before converting, make a backup of your database!</h2></div>
            <h3 class="display-inline"><?php _e('Convert Shopmaster csv file to ali2woo products', 'ali2woo'); ?></h3>
        </div>
        <div class="panel-body">
            <form enctype="multipart/form-data" method="POST">
                <?php if(!isset($upload_state)): ?>
                    <input type="hidden" name="MAX_FILE_SIZE" value="64000000" />
                    <input name="filecsv" type="file" />
                    
                    <div class="pt20">
                        <input class="btn btn-success" type="submit" value="<?php _e('Submit file', 'ali2woo'); ?>" />
                    </div>
                <?php else: ?>
                    <?php if($upload_state['state'] != 'ok'):?>
                        <div class="status error"><?php  _e('Upload error', 'ali2woo'); echo ":".$upload_state['message']; ?></div>
                        <div class="pt20">
                            <input class="btn btn-default" type="submit" name="reset" value="Reset" />
                        </div>
                    <?php else: ?>
                        <div class="convert-log">Found <?php echo count($product_ids); ?> products</div>
                        <input type="hidden" class="convert-file" value="<?php echo $file;?>" />
                        <script type="text/javascript">
                            var a2w_convert_product_ids = <?php echo json_encode($product_ids); ?>;
                        </script>
                        <div class="pt20">
                            <input type="button" class="btn btn-success a2w-convert-products" value="Convert" />  
                            <input class="btn btn-default" type="submit" name="reset" value="Reset" />
                        </div>
                        
                    <?php endif; ?>
                <?php endif; ?>
            </form>
        </div>

        
    </div>
</div>

