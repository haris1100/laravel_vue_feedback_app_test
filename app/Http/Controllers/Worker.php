<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Worker extends Controller
{
    public $allowed_categories = ['bug_report','feature_request','improvement'];

    public function __construct(){



    }

}
