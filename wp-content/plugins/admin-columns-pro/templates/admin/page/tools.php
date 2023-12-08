<h1 class="screen-reader-text"><?= __('Tools', 'codepress-admin-columns') ?></h1>
<div class="ac-section-group -tools <?= esc_attr($this->attr_class) ?>">

	<div class="ac-section-col -left">
        <?php
        foreach ($this->sections as $section) :
            if ($section instanceof ACP\Migrate\Admin\Section\Export) {
                echo $section->render();
            }
        endforeach;
        ?>

	</div>
	<div class="ac-section-col -right">
        <?php
        foreach ($this->sections as $section) :
            if ( ! $section instanceof ACP\Migrate\Admin\Section\Export) {
                echo $section->render();
            }
        endforeach;
        ?>
	</div>


</div>