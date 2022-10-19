<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;
use App\Models\User;
use App\Models\Category;

class AsyncChatAudit extends Model
{
    use HasFactory;

    public $table = 'tam_async_chat_audit';

    protected $dates = [
        'created_at',
        'updated_at',        
    ];

    protected $fillable = [
        'id',
        'session_id',
        'chat_status_old',
        'chat_status_new',
        'changed_by'
    ];
   
}
