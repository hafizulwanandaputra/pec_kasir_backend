<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'price',
        'code_of_service'
    ];

    public function detail_transaction()
    {
        return $this->hasMany(Detail_transaction::class, 'id_service');
    }
}
