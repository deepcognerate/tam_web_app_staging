<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;
use App\Models\User;
use App\Models\Category;

class ChatSessions extends Model
{
    use  HasFactory;

    public $table = 'tam_chat_sessions';

    protected $dates = [
        'created_at',
        'updated_at',        
    ];

    protected $fillable = [
        'session_id',
        'user_id',
        'counsellor_id',
        'category_id',
        'chat_type',
        'assign_by',
        'escalated_status',
        'escalated_reason',
        'escalated_by',
        'timer_status',
        'timer_start_time', 
        'timer_end_time',
        'chat_current_status',
        'close_reason',
        'close_remark',
        'close_issue_code',

        'feedback_comment',
        'feedback_star_reviews'
    ];


    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUserCounselor()
    {
        return $this->belongsTo(User::class, 'counsellor_id');
    }

    public function getCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getCounselor()
    {
        return $this->belongsTo(User::class, 'counsellor_id');
    }

    public function getCounselorAssignBy()
    {
        return $this->belongsTo(User::class, 'assign_by');
    }


     public function chat_history()
    {
        return $this->belongsTo(ChatMessages::class,'session_id','session_id');
    } 
   
}
