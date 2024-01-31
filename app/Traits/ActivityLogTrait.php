<?php
namespace App\Traits;

use App\Core\Traits\SpatieLogsActivity;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

use Auth;
use Carbon\Carbon;

trait ActivityLogTrait 
{

    use SpatieLogsActivity;

    protected static $recordEvents = ['deleted', 'restored', 'created', 'updated'];

    private static $viewableLogs = ["App\\Models\\User", "App\\Models\\UserInfo", "App\\Models\\Company", "App\\Models\\Market", "App\\Models\\Form", "App\\Models\\BlacklistedAddress", "App\\Models\\Recruit"];

    public static function buildLogs($request, $subjectId = "", $subjectType = "")
    {
        $activities = new Activity;

        $activities = $activities->whereIn("subject_type", self::$viewableLogs);
        
        if($request->user && ($request->user != "" && $request->user != "all")) {
            $activities = $activities->where(["causer_id" => $request->user]);
        }        

        if($request->event && $request->event != "all") {
            $activities = $activities->where(["event" => $request->event]);
        }

        if($request->location && $request->location != "all") {
            $location = "App\\Models\\{$request->location}";
            if($request->location == "User") {
                $activities = $activities->whereIn("subject_type", [ $location, "App\\Models\\UserInfo" ]);
            } else {
                $activities = $activities->where(["subject_type" => $location]);                
            }
        }

        if($subjectId && $subjectType) {
            $activities = $activities->where(["subject_id" => $subjectId, "subject_type" => "App\\Models\\". $subjectType]);            
        }        

        if($request->created_at && $request->created_at != "") {
            $dates = explode(" - ", $request->created_at);
            $start = Carbon::parse($dates[0])->startOfDay();
            $end = Carbon::parse($dates[1])->endOfDay();
            $activities = $activities->whereBetween('created_at', [$start, $end]);
        }

        if($request->search && $request->search != "") {
           $activities = $activities->whereRaw("concat(event, ' ', subject_type) like '%" . $request->search . "%' "); 
        }

        $activities = $activities->latest()->paginate(10)
            ->through(function($activity) {
                $activity->causer_name = $activity->causer ? $activity->causer->name : "System";
                $activity->causer_type = $activity->causer ? "user" : "system";
                $activity->description = self::renderDescription($activity);
                $activity->event_type = self::renderEvent($activity->event);
                $activity->location = self::renderLocation($activity->subject_type);
                $activity->properties = count($activity->properties->toArray()) ? self::renderUpdatedProperties($activity) : "";
                $activity->logged_at = $activity->created_at->format('j M Y, g:i A');
                return $activity;
            });
        return $activities;
    }

    public static function buildRecentLogs($request)
    {
        $user = Auth::user();
        $role = $user->renderRole()["description"];
        
        $activities = new Activity;
        
        $activities = $activities->whereIn("subject_type", self::$viewableLogs);

        if($role != "Super Admin") {
            switch($role) {
                case "Agent":
                    $activities = $activities->where(["causer_id" => $user->id]);
                    break;
                case "Coordinator":
                case "Coordinator & Market Director":
                    $userIds = $user->info->company->users->pluck("id")->toArray();
                    $activities = $activities->whereIn("causer_id", $userIds);
                    break;
                case "Market Director":
                    $userIds = $user->info->market->users->pluck("id")->toArray();
                    $activities = $activities->whereIn("causer_id", $userIds);
                    break;
                case "Pod Leader":
                    $userIds = $user->pod_users->pluck("id")->toArray();
                    $activities = $activities->whereIn("causer_id", array_merge($userIds, [$user->id]));
                    break;
            }
        }

        if($request->user && $request->user != "") {
            $activities = $activities->where(["causer_id" => $request->user]);
        }

        if($request->time && $request->time == "today") {
            $start = Carbon::now()->startOfDay();
            $end = Carbon::now()->endOfDay();
            $activities = $activities->whereBetween("created_at", [$start, $end]);
        }

        $limit = 20;
        if($request->limit) {
            $limit = $request->limit;
        }
        
        $activities = $activities->latest()->limit($limit)->get()
            ->map(function($activity) {
                $activity->causer_name = $activity->causer ? $activity->causer->name : "System";
                $activity->causer_type = $activity->causer ? "user" : "system";
                $activity->causer_profile_url = $activity->causer ? $activity->causer->info->renderAvatarUrl() : "";
                $activity->description = self::renderDescription($activity);
                $activity->event_type = self::renderEvent($activity->event);
                $activity->location = self::renderLocation($activity->subject_type);
                $activity->subject_type_icon = self::renderSubjectTypeIcon($activity->subject_type, $activity->event);
                $activity->properties = count($activity->properties->toArray()) ? self::renderUpdatedProperties($activity) : "";
                $activity->logged_at = $activity->created_at->format('j M Y, g:i A');
                $activity->subject = self::renderSubject($activity);
                $activity->time = $activity->created_at->format("H:i");
                return $activity;
            })->values();
        return $activities;
    }

    public static function deleteLog($id)
    {
        $activity = Activity::find($id);
        if($activity) {
            $activity->delete();
        }

        return response()->json([
            "status" => 200,
            "message" => "Activity log has been deleted"
        ]);
    }

    public static function renderUpdatedProperties($activity) 
    {
        $properties = $activity->properties->toArray();
        $watchableFields = self::renderWatchableFields($activity->subject_type);
        $fieldsAttributes = [
            "attributes" => [],
            "old" => [],
        ];
        foreach($watchableFields as $field) {
            if(isset($properties["attributes"])) {
                if(optional($properties["attributes"])[$field["key"]]) {
                    $fieldsAttributes["attributes"][$field["label"]] = optional($properties["attributes"])[$field["key"]];
                }

            }

            if(isset($properties["old"])) {
                if(optional($properties["old"])[$field["key"]]) {
                    $fieldsAttributes["old"][$field["label"]] = optional($properties["old"])[$field["key"]];
                }
            }            
        }
        return $fieldsAttributes;
    }

    public static function renderWatchableFields($subjectType)
    {
        $watchableFields = [];

        switch($subjectType) {
            case "App\\Models\\BlacklistedAddress":
                $watchableFields = [
                    [ "key" => "address_1", "label" => "Address Line 1" ],
                    [ "key" => "address_2", "label" => "Address Line 2" ],
                    [ "key" => "city", "label" => "City" ],
                    [ "key" => "state", "label" => "State" ],
                    [ "key" => "zip", "label" => "Zip" ],
                ];
            break;
            case "App\\Models\\Company":
                $watchableFields = [
                    [ "key" => "company", "label" => "Company Name" ],
                    [ "key" => "phone", "label" => "Phone Number" ],
                    [ "key" => "address_1", "label" => "Address Line 1" ],
                    [ "key" => "address_2", "label" => "Address Line 2" ],
                    [ "key" => "city", "label" => "City" ],
                    [ "key" => "state", "label" => "State" ],
                    [ "key" => "zip", "label" => "Zip" ],
                ];
            break;
            case "App\\Models\\Market":
                $watchableFields = [
                    [ "key" => "company_id", "label" => "Company" ],
                    [ "key" => "market_name", "label" => "Market Name" ],
                    [ "key" => "email", "label" => "Email" ],                                        
                    [ "key" => "phone", "label" => "Phone Number" ],
                    [ "key" => "address_1", "label" => "Address Line 1" ],
                    [ "key" => "address_2", "label" => "Address Line 2" ],
                    [ "key" => "city", "label" => "City" ],
                    [ "key" => "state", "label" => "State" ],
                    [ "key" => "zip", "label" => "Zip" ],
                ];
            break;
            case "App\\Models\\User":
                $watchableFields = [
                    [ "key" => "first_name", "label" => "First Name" ],
                    [ "key" => "last_name", "label" => "Last Name" ],
                    [ "key" => "blacklisted", "label" => "Blacklisted" ],
                ];
            break;
            case "App\\Models\\UserInfo":
                $watchableFields = [
                    [ "key" => "company_id", "label" => "Company" ],
                    [ "key" => "market_id", "label" => "Market" ],
                    [ "key" => "phone", "label" => "Phone Number" ],
                    [ "key" => "avatar", "label" => "Profile Picture" ],
                ];
            break;
            case "App\\Models\\Recruit":
                $watchableFields = [
                    [ "key" => "name", "label" => "Name" ],
                    [ "key" => "phone", "label" => "Phone Number" ],
                    [ "key" => "email", "label" => "Email" ],
                    [ "key" => "company_id", "label" => "Company" ],
                    [ "key" => "market_id", "label" => "Market" ],
                ];
            break;
            case "App\\Models\\Form":
                $watchableFields = [
                    [ "key" => "client_first_name", "label" => "Client First Name" ],
                    [ "key" => "client_last_name", "label" => "Client Last Name" ],
                    [ "key" => "client_dob", "label" => "Client Date of Birth" ],
                    [ "key" => "client_ssn", "label" => "Client SSN" ],
                    [ "key" => "client_sex", "label" => "Client Sex" ],
                    [ "key" => "client_phone", "label" => "Client Phone Number" ],
                    [ "key" => "client_email", "label" => "Client Email" ],
                    [ "key" => "client_address_line_1", "label" => "Client Address Line 1" ],
                    [ "key" => "client_address_line_2", "label" => "Client Address Line 2" ],
                    [ "key" => "client_city", "label" => "Client City" ],
                    [ "key" => "client_state", "label" => "Client State" ],
                    [ "key" => "client_zip", "label" => "Client Zip" ],
                    [ "key" => "product_change", "label" => "Product Change" ],
                    [ "key" => "pregnant", "label" => "Pregnant" ],
                    [ "key" => "married", "label" => "Married" ],
                    [ "key" => "smoker", "label" => "Smoker" ],
                    [ "key" => "dependents", "label" => "Dependents" ], 
                    [ "key" => "american_indian_native", "label" => "American Indian/Alaskan Native" ], 
                    [ "key" => "main_id_type", "label" => "Acceptable Forms of ID" ],
                    [ "key" => "main_id_file", "label" => "Main ID" ],
                    [ "key" => "other_file", "label" => "Other ID" ],
                    [ "key" => "image_with_form", "label" => "Clear Image of Person with Acknowledgment Form" ],
                    [ "key" => "additional_file_one", "label" => "Additional File One" ],
                    [ "key" => "employer_name", "label" => "Employer Name" ],
                    [ "key" => "employer_phone", "label" => "Employer Phone Number" ],
                    [ "key" => "income", "label" => "Income" ],
                    [ "key" => "income_frequency", "label" => "Income Frequency" ],
                    [ "key" => "tax_credit_amount", "label" => "Tax Credit Amount" ],
                    [ "key" => "plan_premium", "label" => "Plan Premium" ],
                    [ "key" => "you_pay", "label" => "You Pay" ],
                    [ "key" => "plan_name", "label" => "Plan Name" ],
                    [ "key" => "insurance", "label" => "Insurance Company" ],
                    [ "key" => "plan_id", "label" => "Plan ID" ],
                    [ "key" => "plan_selected", "label" => "Plan Selected" ],
                    [ "key" => "date_processed", "label" => "Date Processed" ],
                    [ "key" => "cc_number", "label" => "Credit Card" ],
                    [ "key" => "cc_expiration_date", "label" => "Expiration Date" ],
                    [ "key" => "cc_cvc", "label" => "CVC" ],
                    [ "key" => "additional_notes", "label" => "Additional Notes" ],
                    [ "key" => "signature", "label" => "Signature" ],
                    [ "key" => "lat", "label" => "Latitude" ],
                    [ "key" => "long", "label" => "Longitude" ],
                    [ "key" => "status_id", "label" => "Status" ],
                ];
            break;
        }

        return $watchableFields;
    }

    public static function renderEvent($event)
    {
        switch($event) {
            case "created":
                return ["class" => "badge badge badge-light-success", "bg" => "bg-success", "text" => "text-success", "label" => "Created"];
            case "updated":
                return ["class" => "badge badge-light-primary", "bg" => "bg-primary", "text" => "text-primary", "label" => "Updated"];
            case "deleted":
                return ["class" => "badge badge-light-danger", "bg" => "bg-danger", "text" => "text-danger", "label" => "Deleted"];
            case "restored":
                return ["class" => "badge badge-light-success", "bg" => "bg-success", "text" => "text-success", "label" => "Restored"];
            case "deactivated":
                return ["class" => "badge badge-light-danger", "bg" => "bg-danger", "text" => "text-deactivated", "label" => "Deactivated"];
            case "blacklisted":
                return ["class" => "badge badge-light-danger", "bg" => "bg-danger", "text" => "text-danger", "label" => "Blacklisted"];                
            case "activated":
                return ["class" => "badge badge-light-success", "bg" => "bg-success", "text" => "text-success", "label" => "Activated"];                            
            case "logged-in":
                return ["class" => "badge badge-light-dark", "bg" => "bg-light", "text" => "text-muted", "label" => "Logged-in"];
            case "logged-out":
                return ["class" => "badge badge-light-warning", "bg" => "bg-warning", "text" => "text-warning", "label" => "Logged-out"];               
        }
    }

    public static function renderLocation($subjectType)
    {
        $subject = explode("\\", $subjectType);
        $subject = $subject[count($subject) - 1];
        $subject = preg_replace('/(?<!\ )[A-Z]/', ' $0', $subject);
        return $subject;
    }

    public static function renderDescription($activity) 
    {
        switch($activity->subject_type) {
            case "App\\Models\\User":
            case "App\\Models\\UserInfo":
                if(in_array($activity->event, ["logged-in", "logged-out", "deactivated", "activated", "blacklisted"])) {
                    if(($activity->event == "logged-in" || $activity->event == "logged-out") && $activity->causer_id == auth()->user()->id) {
                       return "You've {$activity->event}.";
                    } else {
                       return $activity->description;
                    }
                 } else {
                    $name = $activity->subject->name ? $activity->subject->name : $activity->subject->user->name;
                    return "{$name} information has been {$activity->event}";
                }
            case "App\\Models\\Company":
                return "{$activity->subject->company} has been {$activity->event}";
            case "App\\Models\\Market":
                return "{$activity->subject->market_name} has been {$activity->event}";
            case "App\\Models\\Form":
                return "{$activity->subject->name} form has been {$activity->event}";
            case "App\\Models\\BlacklistedAddress":
                return "Blacklisted Address has been {$activity->event}";
            case "App\\Models\\Recruit":
                return "{$activity->subject->name} has been {$activity->event}";
            default:
                return $activity->description;                                                        
        }
    }

    public static function renderSubjectTypeIcon($subjectType, $event) 
    {
        $color = "";
        if($event != "logged-in") {
            $color = "text-white";
        }

        switch($subjectType) {
            case "App\\Models\\User":
            case "App\\Models\\UserInfo":
                return theme()->getSvgIcon("demo1/media/icons/duotune/abstract/abs029.svg", "svg-icon-2 {$color}");
            case "App\\Models\\Company":
                return theme()->getSvgIcon("demo1/media/icons/duotune/finance/fin001.svg", "svg-icon-2 {$color}");
            case "App\\Models\\Market":
                return theme()->getSvgIcon("demo1/media/icons/duotune/maps/map002.svg", "svg-icon-2 {$color}");
            case "App\\Models\\Form":
                return theme()->getSvgIcon("demo1/media/icons/duotune/files/fil003.svg", "svg-icon-2 {$color}");
            case "App\\Models\\BlacklistedAddress":
                return "<i class='fa-solid fa-ban {$color}'></i>";
            case "App\\Models\\Recruit":
                return theme()->getSvgIcon("demo1/media/icons/duotune/general/gen019.svg", "svg-icon-2 {$color}");
        }
    }

    public static function renderSubject($log) 
    {
        if(in_array($log->subject_type, ["App\\Models\\User", "App\\Models\\UserInfo"])) {
            $log->subject_data = $log->subject_type == "App\\Models\\User" ? $log->subject : $log->subject->user;
            $log->subject_info = $log->subject_type == "App\\Models\\User" ? $log->subject->info : $log->subject; 
            return $log;
        }
        return $log->subject;
    }
}