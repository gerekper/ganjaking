<?php

namespace DynamicContentForElementor;

trait Notices
{
    /**
     * Notice
     *
     * @param string $title
     * @param string $content
     * @param string $class
     * @param string $id
     * @return void
     */
    public static function notice($title = '', $content = '', $class = 'elementor-alert-info', $id = '')
    {
        ?>
	<div <?php 
        echo $id ? "id='{$id}'" : '';
        ?>  class="elementor-alert <?php 
        echo $class;
        ?> " role="alert">
		<?php 
        if ($title) {
            ?>
			<span class="elementor-alert-title"><?php 
            echo wp_kses_post($title);
            ?></span>
		<?php 
        }
        if ($content) {
            ?>
			<span class="elementor-alert-description"><?php 
            echo wp_kses_post($content);
            ?></span>
		<?php 
        }
        ?>
	</div>
	<?php 
    }
}
