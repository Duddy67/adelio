<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UsersUsers
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

        $create = ['users.users.index', 'users.users.create', 'users.users.store'];
        $update = ['users.users.update', 'users.users.edit'];
        $delete = ['users.users.destroy', 'users.users.massDestroy'];

        if (in_array($routeName, $create) && !auth()->user()->isAllowedTo('create-user')) {
            return redirect()->route('index')->with('error', __('messages.generic.access_not_auth'));
        }

        if (in_array($routeName, $update) && !auth()->user()->isAllowedTo('update-user')) {
            return redirect()->route('users.users.index')->with('error', __('messages.users.edit_not_auth'));
        }

        if (in_array($routeName, $delete) && !auth()->user()->isAllowedTo('delete-user')) {
            return redirect()->route('users.users.index')->with('error', __('messages.users.delete_not_auth'));
        }

        return $next($request);
    }
}
