<div id="gpbua-tabs">

  <div id="tabs">

    <ul>
      <?php foreach( $tabs as $tab_index => $tab ) : ?>
        <li class="tab-menu-<?php print $tab['key']; ?>"><a href="#tabs-<?php print $tab_index; ?>"><?php print $tab['title']; ?></a></li>
      <?php endforeach; ?>
    </ul>

      <div class="gpbua-tabs-container">
        <?php foreach( $tabs as $tab_index => $tab ) : ?>
          <div id="tabs-<?php print $tab_index; ?>" class="gpbua-tab tab-<?php print $tab['key']; ?>">
            <?php wp_editor( $tab['content'], $tab['field_id'], array( 'editor_height' => 350 ) ); ?>
              <div class="gpbua-tab-footer">
                  <?php
                  $this->render_merge_tag_select( $tab['key'] );
                  $this->render_reset_content_button( $tab['key'] );
                  ?>
              </div>
          </div>
        <?php endforeach; ?>
      </div>

  </div>

</div>