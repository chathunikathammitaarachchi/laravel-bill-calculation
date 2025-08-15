<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customer';

    public $incrementing = false;  // Because customer_id is manually assigned
    protected $primaryKey = 'customer_id';
    protected $keyType = 'int';

    protected $fillable = [
        'customer_id',
        'customer_name',
        'phone',
    ];
}
