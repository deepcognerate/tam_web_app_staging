<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class ResumeChat extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'tam_resume_chat';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'id',
        'status',
        'user_id',
        'counsellor_id',
    ];


}