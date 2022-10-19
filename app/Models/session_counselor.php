<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class session_counselor extends Model
{
    use HasFactory;
    public $table = 'tam_session_counselor';
    protected $fillable = [
        'id',
        'session_id',
        'counselor_id',
        'created_at',
        'updated_at'
    ];
}
