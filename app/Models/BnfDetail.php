<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class BnfDetail extends Model
{
    use HasFactory;
    protected $collection = 'bnf_details';
}
