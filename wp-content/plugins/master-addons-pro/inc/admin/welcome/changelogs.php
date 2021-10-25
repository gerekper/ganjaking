<div class="wp-tab-panel" id="changelogs" style="display: none;">
	<div class="parent">
		<div class="left_column">
			<?php
				require_once( MELA_PLUGIN_PATH . '/lib/readme-parser.php' );
				$li = new WordPress_Readme_Parser();
				$t = $li->parse_readme( MELA_PLUGIN_PATH . '/readme.txt' );
				echo '<pre>' . $t['sections']['changelog'] . '</pre>';
			?>
		</div>
		<?php require( MELA_PLUGIN_PATH . '/inc/admin/welcome/right-column.php' );?>
	</div>
</div>