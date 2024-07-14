<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LastBilledReading extends Model
{
    use HasFactory;

    protected $fillable = ['meter_name', 'reading_value'];
}