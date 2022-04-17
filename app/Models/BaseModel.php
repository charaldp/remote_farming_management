<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function setObjectAttribute($array_attribute, $key, $value)
    {
        $array_attribute_copy = $this->$array_attribute;
        $array_attribute_copy[$key] = $value;
        $this->$array_attribute = $array_attribute_copy;
    }
}
