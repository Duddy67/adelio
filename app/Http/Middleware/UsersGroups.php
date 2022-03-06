<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UsersGroups
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

        $create = ['users.groups.index', 'users.groups.create', 'users.groups.store'];
        $update = ['users.groups.update', 'users.groups.edit'];
        $delete = ['users.groups.destroy', 'users.groups.massDestroy'];

        if (in_array($routeName, $create) && !auth()->user()->isAllowedTo('create-group')) {
            return redirect()->route('index')->with('error', __('messages.generic.access_not_auth'));
        }

        if (in_array($routeName, $update) && !auth()->user()->isAllowedTo('update-group') && !auth()->user()->isAllowedTo('update-own-group')) {
            return redirect()->route('users.groups.index')->with('error', __('messages.groups.edit_not_auth'));
        }

        if (in_array($routeName, $delete) && !auth()->user()->isAllowedTo('delete-group') && !auth()->user()->isAllowedTo('delete-own-group')) {
            return redirect()->route('users.groups.index')->with('error', __('messages.groups.delete_not_auth'));
        }

        return $next($request);
    }
}
