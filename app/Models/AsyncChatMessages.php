<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class AsyncChatMessages extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'tam_async_chat_messages';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'id',
        'session_id',
        'sender_id',
        'reciver_id',
        'message',
        'status',
        'chatAttachment',
        'msgType',
        'read_status'        
    ];
    
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
   
}