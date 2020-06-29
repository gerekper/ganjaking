<?php
// {$setting_id}[$id] - Contains the setting id, this is what it will be stored in the db as.
// $class - optional class value
// $id - setting id
// $options[$id] value from the db

$option_values = array(
	'01'=>__('01-Jan','seedprod-coming-soon-pro'),
	'02'=>__('02-Feb','seedprod-coming-soon-pro'),
	'03'=>__('03-Mar','seedprod-coming-soon-pro'),
	'04'=>__('04-Apr','seedprod-coming-soon-pro'),
	'05'=>__('05-May','seedprod-coming-soon-pro'),
	'06'=>__('06-Jun','seedprod-coming-soon-pro'),
	'07'=>__('07-Jul','seedprod-coming-soon-pro'),
	'08'=>__('08-Aug','seedprod-coming-soon-pro'),
	'09'=>__('09-Sep','seedprod-coming-soon-pro'),
	'10'=>__('10-Oct','seedprod-coming-soon-pro'),
	'11'=>__('11-Nov','seedprod-coming-soon-pro'),
	'12'=>__('12-Dec','seedprod-coming-soon-pro'),
	);

_e('Start Date', 'seedprod-coming-soon-pro');
echo "<select id='mm' name='{$setting_id}[$id][start_month]'>";
foreach ( $option_values as $k => $v ) {
    echo "<option value='$k' " . selected( $options[ $id ]['start_month'], $k, false ) . ">$v</option>";
}
echo "</select>";

echo "<input id='jj' class='small-text' placeholder='".__('day','seedprod-coming-soon-pro')."' name='{$setting_id}[$id][start_day]' type='text' value='" . esc_attr( $options[ $id ]['start_day'] ) . "' />";

echo ',';
echo "<input id='aa' class='small-text' placeholder='".__('year','seedprod-coming-soon-pro')."' name='{$setting_id}[$id][start_year]' type='text' value='" . esc_attr( $options[ $id ]['start_year'] ) . "' />";

echo '&nbsp;&nbsp;&nbsp;&nbsp;';
_e('End Date', 'seedprod-coming-soon-pro');
echo "<select id='mm' name='{$setting_id}[$id][end_month]'>";
foreach ( $option_values as $k => $v ) {
    echo "<option value='$k' " . selected( $options[ $id ]['end_month'], $k, false ) . ">$v</option>";
}
echo "</select>";

echo "<input id='jj' class='small-text' placeholder='".__('day','seedprod-coming-soon-pro')."' name='{$setting_id}[$id][end_day]' type='text' value='" . esc_attr( $options[ $id ]['end_day'] ) . "' />";

echo ',';
echo "<input id='aa' class='small-text' placeholder='".__('year','seedprod-coming-soon-pro')."' name='{$setting_id}[$id][end_year]' type='text' value='" . esc_attr( $options[ $id ]['end_year'] ) . "' /><br>";

