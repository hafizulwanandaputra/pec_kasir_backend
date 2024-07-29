<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail_transaction extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'id_service',
        'id_transaction',
        'time',
        'subtotal',
        'quantity',
        'discount',
        'total_price',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'id_service');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'id_transaction');
    }
}
