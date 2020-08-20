<?php
/**
 * Created by PhpStorm.
 * User: wedevs-macbook-2
 * Date: 16/10/18
 * Time: 3:26 PM
 */

namespace WeDevs\PM_Pro\Modules\task_recurring;

use WeDevs\PM\Common\Models\Meta;
use Carbon\Carbon;

class FormatRecurrenceData {

    public $type;
    public $weekdays;
    public $startFrom;
    public $meta_id;
    public $unit = ['1' => "week", '2' => "month", '3' => "year" ];
    public $recurrence;


    public function __construct( $recurrent_data ) {
        $this->type       = $recurrent_data['recurrent'];
        $this->meta_id    = $recurrent_data['meta_id'];
        $this->recurrence = $recurrent_data['recurrence'];
        $this->weekdays   = $recurrent_data['recurrence']['weekdays'];
        $this->startFrom  = $recurrent_data['created_at']->toDateString();

        $this->update_unit_type();

        if($this->recurrence['formatted'] == '0') {
            $this->recurrence['formatted'] = '1';
            $this->recurrence['last_run'] = Carbon::now()->subDay(1)->toDateString();
            $this->update_meta();
        }

    }

    public function update_unit_type() {
        switch ($this->type) {
            case "1":
                $this->recurrence[$this->unit[$this->type]] = Carbon::parse($this->startFrom)->startOfWeek()->toDateString();
                break;
            case "2":
                $now = Carbon::parse($this->startFrom);
                $year  = $now->year;
                $month = $now->month;
                $day   = $this->recurrence['repeat'] > 29 ? $now->endOfMonth()->day : $this->recurrence['repeat'];
                $day   = empty( absint( $day ) ) ? 1 : $day;
                $monthly_date = Carbon::parse($year.'-'.$month.'-'.$day);
                if($monthly_date->lessThan(Carbon::now())){
                    $monthly_date->addMonth(1);
                }
                $this->recurrence[$this->unit[$this->type]] = $monthly_date->toDateString();
                break;
            case "3":
                $repeat_year = Carbon::parse($this->recurrence['repeat_year']);
                if($repeat_year->lessThan(Carbon::now())){
                    $repeat_year->addYear(1);
                }
                $this->recurrence[$this->unit[$this->type]] = $repeat_year->toDateString();
                break;
        }
    }


    public function is_expired(){

        switch ($this->recurrence['expire_type'] ) {
            case "occurrence":
//                if ($this->recurrence['occurrence_attempted'] === $this->recurrence['expire_after_occurrence']) {
                if ($this->recurrence['expire_after_occurrence'] == 0) {
                    return true;
                } else
                { return false; }
                break;
            case "date":
                if ( Carbon::now()->greaterThan(Carbon::parse($this->recurrence['expire_after_date']))) {
                    return true;
                } else
                { return false; }

                break;
            case "n":
                return false;
                break;
        }

    }
    public function update_recurrence(){

        switch ($this->recurrence['expire_type'] ) {
            case "occurrence":
                if($this->recurrence['expire_after_occurrence'] != 0) {
                    $this->recurrence['occurrence_attempted'] += 1;
                    $this->recurrence['expire_after_occurrence'] -= 1;
                }
                break;
            case "date":

                break;
            case "n":

                break;
        }

    }

    public function parseWeekdays(){
        $weekday_new = [];
        foreach ($this->weekdays as $weekday){
            $checked = json_decode($weekday['checked']); //=== 'true'? true: false;
            array_push($weekday_new, [
                "name" => $weekday['name'],
                "value" => $weekday['value'],
                "checked" => $checked,
            ]);
        }
        return $weekday_new;
    }

    public function update_meta(){
        $meta = Meta::find($this->meta_id);
        $meta->update(['meta_value'=>serialize($this->recurrence)]);
    }

    public function updateAfterRun(){
        $this->recurrence['last_run'] = Carbon::now()->toDateString();
        $this->update_recurrence();
        $this->update_meta();
    }

}
