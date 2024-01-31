<?php

namespace App\Http\Middleware;

use Closure;

class AccessCheckerMiddleware
{
    protected $generalRoutes = [
        "markets.fetch", "companies.fetch", "users.fetch",
        "overview.index", "settings.index", "settings.update",
        "settings.changeEmail", "settings.changePassword", "users.find",
        "logout", "login", "home", "users.login-sessions", "faqs.index",
        "activity-logs.recent", "users.update-theme", "settings.updateTwoAuth",
        "form-templates.fetch-active", "activity-logs.fetch", "users.get", "settings.verify",
        "statuses.fetch", "activity-logs.fetch-by-subject"
    ];
    
    public function __construct() {

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        if(!$request->expectsJson()) {
            if($user && !$user->isVerified() && ($request->route()->uri != "index" && $request->route()->uri != "/")) {
                return redirect()->route("dashboard", "verified=false");
            }
        }
        
        $currRoute = $request->route()?->getName();
        if(!$currRoute && $request->route()->uri == "/") {
            return $next($request);            
        }
        
        if(in_array($currRoute, $this->generalRoutes)) {
            return $next($request);
        }

        if($user && !$user->superAdmin()) {
            $userAccess = $user->getMyAccessList();
            $status = false;
            foreach($userAccess as $access) {
                foreach($access["routes"] as $route) {
                    if($currRoute == $route) {
                        $status = true;
                    }             
                }
            } 
            if(!$status) {
                if($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Permission denied.',
                    ], 403);
                } else {
                    return redirect()->back();
                }

            }
        }
        return $next($request);
    }

}
