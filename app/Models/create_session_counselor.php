<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class create_session_counselor extends Model
{
    use HasFactory;
    public $table = "tam_create_counselor_session";

    protected $fillable = [
        'id',
        'session_title',
        'session_description',
        'start_time',
        'Duration',
        'session_type'
    ];
}
