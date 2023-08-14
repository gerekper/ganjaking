<select class="dashboard-search-field select-kb-top" name="knowledgebase">
	<option value="all"><?php _e( 'All Knowledge Base', 'betterdocs-pro' );?></option>
	<?php
        $_current_term = ( isset( $_GET['knowledgebase'] ) ) ? $_GET['knowledgebase'] : '';
        echo betterdocs()->template_helper->term_options( 'knowledge_base', $_current_term );
    ?>
</select>