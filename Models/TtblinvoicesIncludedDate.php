<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TtblinvoicesIncludedDate extends Model
{
    use SoftDeletes;

    protected $table = "tblinvoices_includeddates";

    protected $fillable = [
        'InvoiceID',
        'EID',
        'DID',
        'Amount'
    ];

    protected $primaryKey = 'ID';
    
}