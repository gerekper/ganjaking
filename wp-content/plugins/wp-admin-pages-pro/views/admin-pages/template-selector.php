<div v-cloak id="html-editor" v-show="content_type == '<?php echo $config->id; ?>'" style="margin-top: 12px;">

  <div class="postbox" style="margin-bottom: 0;">

    <button type="button" class="handlediv button-link" aria-expanded="true">
      <span class="screen-reader-text"><?php printf(__('Toggle panel: %s', 'wu-apc'), $config->selector_title); ?></span>
      <span class="toggle-indicator" aria-hidden="true"></span>
    </button>

    <h2 class="hndle ui-sortable-handle">
      <span>
        <?php echo $config->selector_title; ?>
      </span>
    </h2>

    <div class="inside">

      <p>
        <?php printf(__('You can select a custom %s template to be used as the content of this admin page.', 'wu-apc'), $config->title); ?> <a target="_blank" href="<?php echo $config->read_more_link; ?>"><?php printf(__('More about %s and its templates.', 'wu-apc'), $config->title); ?></a>
      </p>

      <p class="bb-selector">
        <label class="" for="template">
          <?php
          // translators: %s is something like "Elementor Template".
          printf(__('Select the %s', 'wu-apc'), $config->selector_title);
          ?>
        </label>

        <select placeholder="<?php printf(__('No %s', 'wp-apc'), $config->selector_title); ?>" id="template" name="template_id" v-model="template_id">

          <option value=""><?php _e('Select a Template', 'wp-apc'); ?></option>

          <option v-bind:selected="template.ID == <?php echo $admin_page->{$config->field} ?: 0; ?>" v-if="<?php echo "wu_apc_{$config->id}_options"; ?>.templates.length" v-for="template in <?php echo "wu_apc_{$config->id}_options"; ?>.templates" v-bind:value="template.ID"><% template.title %></option>

        </select>

        <div class="clear"></div>
    </div>

    <div id="major-publishing-actions" style="text-align: right;">

      <a v-if="template_id != false && <?php echo "wu_apc_{$config->id}_options"; ?>.config.edit_link" title="<?php _e('Inline Editor', 'wu-apc'); ?>" target="_blank" v-bind:class="<?php echo "wu_apc_{$config->id}_options"; ?>.config.supports_modal.indexOf('edit_link') !== -1 ? 'thickbox' : ''" class="button" v-bind:href="'<?php echo $page_builder->get_link('edit_link'); ?>'.replace('TEMPLATE_ID', template_id)">
        <?php _e('Edit Template', 'wu-apc'); ?>
      </a>

      <a target="_blank" v-bind:class="<?php echo "wu_apc_{$config->id}_options"; ?>.config.supports_modal.indexOf('see_all_link') !== -1 ? 'thickbox' : ''" class="button" href="<?php echo $page_builder->get_link('see_all_link'); ?>">
        <?php _e('See all Templates', 'wu-apc'); ?>
      </a>

      <a target="_blank" v-bind:class="<?php echo "wu_apc_{$config->id}_options"; ?>.config.supports_modal.indexOf('add_new_link') !== -1 ? 'thickbox' : ''" class="button" href="<?php echo $page_builder->get_link('add_new_link'); ?>">
        <?php _e('Add new Template', 'wu-apc'); ?>
      </a>

      <div class="clear"></div>

    </div>

  </div>

</div>