<select class="dashboard-search-field dashboard-select-kb" name="knowledgebase" id="dashboard-select-kb">
	<option value="all"><?php _e( 'All KBs', 'betterdocs-pro' );?></option>
	<?php
        $_current_term = ( isset( $_GET['knowledgebase'] ) ) ? trim( $_GET['knowledgebase'] ) : '';
        echo betterdocs()->template_helper->term_options( 'knowledge_base', $_current_term );
    ?>
</select>