<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }
use \memberpress\courses\lib\Utils;

if(!empty($rows)) {
  foreach($rows as $row) {
    ?>
    <tr id="mpcs-attempt-row-<?php echo esc_attr($row->id); ?>">
      <?php
        foreach($columns as $column_name => $column_display_name) {
          $classes = ["$column_name column-$column_name"];

          if($primary === $column_name) {
            $classes[] = 'has-row-actions column-primary';
          }

          if(in_array($column_name, $hidden, true)) {
            $classes[] = 'hidden';
          }

          $attributes = 'class="' . esc_attr(join(' ', $classes)) . '"';

          switch($column_name) {
            case 'cb': ?>
              <th scope="row" class="check-column">
                <label class="screen-reader-text" for="cb-select-<?php echo esc_attr($row->id); ?>">
                  <?php esc_html_e('Select attempt', 'memberpress-courses'); ?>
                </label>
                <input type="checkbox" name="att[]" id="cb-select-<?php echo esc_attr($row->id); ?>" value="<?php echo esc_attr($row->id); ?>">
              </th>
              <?php break;
            case 'col_name': ?>
              <td <?php echo $attributes; ?>>
                <?php
                  $edit_link = add_query_arg(['user_id' => (int) $row->user_id], admin_url('user-edit.php'));
                  $name = Utils::name_or_username($row->first_name, $row->last_name, $row->user_login);
                ?>
                <a href="<?php echo esc_url($edit_link); ?>"><?php echo esc_html($name); ?></a>
                <div class="row-actions">
                  <span class="view">
                    <?php
                      printf(
                        '<a href class="mpcs-quiz-attempt-view" data-id="%s" aria-label="%s">%s</a>',
                        esc_attr($row->id),
                        esc_attr(sprintf(__('View attempt by &#8220;%s&#8221;', 'memberpress-courses'), $name)),
                        esc_attr__('View', 'memberpress-courses')
                      );
                    ?>
                  </span>
                  |
                  <span class="delete">
                    <?php
                      printf(
                        '<a href class="mpcs-quiz-attempt-delete" data-id="%s" aria-label="%s">%s</a>',
                        esc_attr($row->id),
                        esc_attr(sprintf(__('Delete attempt by &#8220;%s&#8221;', 'memberpress-courses'), $name)),
                        esc_attr__('Delete', 'memberpress-courses')
                      );
                    ?>
                  </span>
                </div>
              </td>
              <?php break;
            case 'col_score': ?>
              <td <?php echo $attributes; ?>>
                <?php
                  echo esc_html(
                    sprintf(
                      /* translators: %1$s: points awarded, %2$s: points possible, %3$s: score percent, %%: literal percent sign */
                      __('%1$s/%2$s (%3$s%%)', 'memberpress-courses'),
                      $row->points_awarded,
                      $row->points_possible,
                      $row->score
                    )
                  );
                ?>
              </td>
              <?php break;
            case 'col_finished_at': ?>
              <td <?php echo $attributes; ?>>
                <?php echo esc_html(Utils::format_datetime($row->finished_at)); ?>
              </td>
              <?php break;
          }
        }
      ?>
    </tr>
    <?php
  }
}
