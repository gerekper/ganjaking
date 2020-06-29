<?php
defined('WYSIJA') or die('Restricted access');
/*
 Class WJ_FieldHandler
 It handles the custom fields in a controller.
 */
class WJ_FieldHandler {

    public function __construct() {
    }

    /*
    Handler to save all the fields, by giving an array
    that specifies the custom fields columns names, and
    the ID of the user.
    WJ_FieldHandler::handle_all(array(
      'cf_1' => 'Value',
      'cf_2' => 'Value 2'
      ), 1
    );
    */
    public static function handle_all(array $fields_values, $user_id) {
      $user_fields = WJ_FieldUser::get_all($user_id);

      if(isset($user_fields) && !empty($user_fields)) {
        foreach ($user_fields as $user_field) {
          $key = $user_field->column_name();

          // check if there is a value for this custom field
          if(array_key_exists($key, $fields_values)) {

            // set value
            $new_value = $fields_values[$key];

            // extra process for specific types
            if($user_field->field->type === 'checkbox') {
              // limit the value between [0,1]
              $new_value = min(max(0, (int)$new_value), 1);

            } else if($user_field->field->type === 'date') {
              // get date parameters
              $year = (isset($fields_values[$key]['year'])) ? (int)$fields_values[$key]['year'] : (int)strftime('%Y');
              $month = (isset($fields_values[$key]['month'])) ? (int)$fields_values[$key]['month'] : 1;
              $day = (isset($fields_values[$key]['day'])) ? (int)$fields_values[$key]['day'] : 1;

              $new_value = null;
              if($year !== 0 && $month !== 0 && $day !== 0) {
                // make timestamp from date parameters
                $new_value = mktime(0, 0, 0, $month, $day, $year);
              }
            }

            // update value
            $user_field->update($new_value);
          }
        }
      }
    }
}
