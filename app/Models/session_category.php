<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class session_category extends Model
{
    use HasFactory;
    public $table = 'tam_session_category';
    protected $fillable =[
        'id',
        'session_id',
        'category_id',
        'created_at',
        'updated_at'
    ];
}
