<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;
use App\Models\User;
use App\Models\Category;

class Feature extends Model
{
    use HasFactory;

    public $table = 'tam_feature';

    protected $dates = [
        'created_at',
        'updated_at',        
    ];

    protected $fillable = [
        'id',
        'feature_name'
    ];
   
}
