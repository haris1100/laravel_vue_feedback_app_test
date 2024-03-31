<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users_linked(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function comments(){
        return $this->hasMany(FeedbackComments::class,'feedback_id')->orderBy('id','Desc');
    }
}
