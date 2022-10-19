<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;
use App\Models\User;
use App\Models\Category;

class ChatFeature extends Model
{
   use SoftDeletes, HasFactory;

    public $table = 'tam_counsellor_feature';

    protected $dates = [
        'created_at',
        'updated_at', 
        'deleted_at',       
    ];

    protected $fillable = [
        'id',
        'counsellor_id',
        'feature_id'        
    ];
   
}
