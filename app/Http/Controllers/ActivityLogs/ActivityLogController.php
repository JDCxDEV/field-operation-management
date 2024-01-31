<?php

namespace App\Http\Controllers\ActivityLogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ActivityLogTrait;

class ActivityLogController extends Controller
{

    public function index() 
    {
        return view("pages.activity-logs.index");
    }
    
    public function fetch(Request $request) 
    {
        return ActivityLogTrait::buildLogs($request);
    }

    public function fetchBySubject($subjectId, $subjectType, Request $request)
    {
        return ActivityLogTrait::buildLogs($request, $subjectId, $subjectType);        
    }

    public function recent(Request $request) 
    {
        return ActivityLogTrait::buildRecentLogs($request);
    }

    public function destroy($id)
    {
        return ActivityLogTrait::deleteLog($id);        
    }
}
