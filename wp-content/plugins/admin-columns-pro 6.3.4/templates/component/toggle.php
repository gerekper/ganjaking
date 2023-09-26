<label class="ac-toggle-container" data-ac-toggle="<?= $this->name; ?>">
	<span class="ac-toggle">
        <input type="checkbox" value="1" <?php if ( $this->checked ): ?>checked="checked"<?php endif; ?>>
        <span class="ac-toggle__switch">
            <svg class="ac-toggle__switch__on" width="2" height="6" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6"><path fill="#fff" d="M0 0h2v6H0z"></path></svg>
            <svg class="ac-toggle__switch__off" width="6" height="6" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6"><path fill="#fff" d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path></svg>
            <span class="ac-toggle__switch__track"></span>
        </span>
    </span>
	<span class="ac-toggle-label"><?= $this->label ?></span>
</label>