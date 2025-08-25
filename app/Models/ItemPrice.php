<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemPrice extends Model
{
    use HasFactory;

    protected $table = 'item_price_details';

    // ONLY 'item_id' and 'rate' are fillable
    protected $fillable = ['item_id', 'rate','cost_price'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
