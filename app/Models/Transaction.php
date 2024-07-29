<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'id_patient',
        'id_outpatient',
        'id_poli',
        'id_doctor',
        'date',
        'payment_methode',
        'total_transaction',
        'remaining_payment',
        'amount',
        'return_amount',
        'payment_status'
    ];

    public function detail_transaction()
    {
        return $this->hasMany(Detail_transaction::class, 'id_transaction');
    }

    public function outpatient()
    {
        return $this->belongsTo(Outpatient::class, 'id_outpatient');
    }
}
