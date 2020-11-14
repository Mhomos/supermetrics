<?php

namespace App\Models;

use App\Helpers\HelperMethods;
use Carbon\Carbon;

class Post
{
    protected $fillable = ['id', 'from_name', 'from_id', 'message', 'type', 'created_time'];

    public $id;
    public $from_name;
    public $from_id;
    public $message;
    public $message_length;
    public $type;
    public $created_time;
    public $year;
    public $month;
    public $week;
    public $day;

    public function __construct($data)
    {
        $this->setData($data);

        return $this;
    }

    public function setData($data)
    {
        foreach ($this->fillable as $field) {
            $val = $data->$field ?? null;
            $setFieldMethod = HelperMethods::toCamelCase("set_" . $field . "_field");
            if (method_exists($this, $setFieldMethod)) {
                $val = $this->$setFieldMethod($val);
            }
            $this->$field = $val;
        }
    }

    protected function setMessageField($val)
    {
        $this->message_length = strlen($val);

        return $val;
    }

    protected function setCreatedTimeField($val)
    {
        $date = Carbon::parse($val);

        $this->year = $date->year;
        $this->month = $date->month;
        $this->week = $date->week;
        $this->day = $date->day;

        return $date->format('Y-m-d');
    }
}
