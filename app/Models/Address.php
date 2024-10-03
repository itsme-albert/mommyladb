<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $table = 'address'; // Explicitly set the table name if it doesn't follow Laravel's naming convention

    protected $fillable = [
        'house_number', 
        'street', 
        'barangay', 
        'city', 
        'province', 
        'postal_code',
        'country'
    ];
}
