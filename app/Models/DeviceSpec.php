<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceSpec extends Model
{
    protected $fillable = ['device_id', 'category', 'spec_key', 'spec_value', 'sort_order'];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
