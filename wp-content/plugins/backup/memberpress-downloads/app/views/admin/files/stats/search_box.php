<span class="filter-by">
  <label><?php _e('Filter by', 'memberpress', 'memberpress-downloads'); ?></label>

  <!-- <select class="mepr_filter_field" id="filter">
    <option value="file" <?php //selected($file_name, false); ?>><?php //_e('File Name', 'memberpress-downloads'); ?></option>
  </select> -->

  <input
    type="text"
    name="mepr_access_row[condition][]"
    class="mpdl_suggest_files mepr_filter_field mepr-rule-access-condition-input"
    placeholder="<?php _e('File Name', 'memberpress-downloads'); ?>"
    id="file_name"
    value="<?php esc_attr_e($file_name, 'memberpress-downloads') ?>"
  ></input>

  <input
    type="text"
    name="mepr_access_row[condition][]"
    class="mepr_filter_field datepicker mepr-rule-access-condition-input"
    placeholder="<?php _e('Start Date', 'memberpress-downloads'); ?>"
    id="start_date"
    value="<?php esc_attr_e($start_date, 'memberpress-downloads') ?>"
  ></input>

  <input
    type="text"
    name="mepr_access_row[condition][]"
    class="mepr_filter_field datepicker mepr-rule-access-condition-input"
    placeholder="<?php _e('End Date', 'memberpress-downloads'); ?>"
    id="end_date"
    value="<?php esc_attr_e($end_date, 'memberpress-downloads') ?>"
  ></input>

  <input type="submit" id="mepr_search_filter" class="button" value="<?php _e('Go', 'memberpress-downloads'); ?>" />

  <?php
    if(isset($_REQUEST['file_name']) || isset($_REQUEST['start_date']) || isset($_REQUEST['end_date'])) {
      $uri = $_SERVER['REQUEST_URI'];
      $uri = preg_replace('/[\?&]file_name=[^&]*/','',$uri);
      $uri = preg_replace('/[\?&]start_date=[^&]*/','',$uri);
      $uri = preg_replace('/[\?&]end_date=[^&]*/','',$uri);
      ?>
      <a href="<?php echo $uri; ?>">[x]</a>
      <?php
    }
  ?>
</span>

<?php /* _e('or', 'memberpress'); */ ?>
