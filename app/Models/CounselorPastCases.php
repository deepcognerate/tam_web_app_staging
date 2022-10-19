<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class CounselorPastCases extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'tam_counselor_past_cases';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'id',
        'date',
        'category_id',
        'user_id',
        'counselor_id',
        'chat_type',
        'reason',
        'remark',
        'issue_code',
        'chatReason',
        'assign_by',
        'counselorName',
        'counselor_to_admin',
        'feedback_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getCategory()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }

    public function getCounselor()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function getUser()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

     public function getCounselorget()
    {
        return $this->belongsTo(User::class,'counselor_id','id');
    }

    public function chat_history()
    {
        return $this->belongsTo(ChatHistory::class,'id','counselor_past_cases_id');
    }

    public function getFeedback()
    {
        return $this->belongsTo(Feedback::class,'feedback_id','id');
    }
}