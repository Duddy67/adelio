<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlogSettings
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
        $routeName = $request->route()->getName();

        if ($routeName == 'blog.settings.index' && !auth()->user()->isAllowedTo('blog-settings')) {
            return redirect()->route('index')->with('error', __('messages.generic.access_not_auth'));
        }

        return $next($request);
    }
}
