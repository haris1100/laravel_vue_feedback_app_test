<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackComments extends Model
{
    use HasFactory;
    protected  $guarded = [];

    public function users_linked(){
        return $this->belongsTo(User::class,'commented_by');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
