<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class DmAndDIngredient extends Model
{
    use HasFactory;
    protected $collection = 'dm_and_d_ingredients';
}
