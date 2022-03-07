<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Users\Role;
use App\Models\Settings\General;
use Cache;


class Index 
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (in_array(auth()->user()->getRoleType(), ['super-admin', 'admin', 'manager', 'assistant'])) {

            $settings = Cache::rememberForever('settings', function () {
                // Updates the config app parameters.
                return  General::getAppSettings();
            });

            config($settings); // Any DB settings will overwrite app config

            return $next($request);
        }

        abort(403);
        //return redirect()->route('profile');
    }
}
