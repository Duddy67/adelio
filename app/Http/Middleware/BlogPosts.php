<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlogPosts
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

        $create = ['blog.posts.index', 'blog.posts.create', 'blog.posts.store'];
        $update = ['blog.posts.update', 'blog.posts.edit'];
        $delete = ['blog.posts.destroy', 'blog.posts.massDestroy'];

        if (in_array($routeName, $create) && !auth()->user()->isAllowedTo('create-post')) {
            return redirect()->route('index')->with('error', __('messages.generic.access_not_auth'));
        }

        if (in_array($routeName, $update) && !auth()->user()->isAllowedTo('update-post') && !auth()->user()->isAllowedTo('update-own-post')) {
            return redirect()->route('blog.posts.index')->with('error', __('messages.posts.edit_not_auth'));
        }

        if (in_array($routeName, $delete) && !auth()->user()->isAllowedTo('delete-post') && !auth()->user()->isAllowedTo('delete-own-post')) {
            return redirect()->route('blog.posts.index')->with('error', __('messages.posts.delete_not_auth'));
        }

        return $next($request);
    }
}
