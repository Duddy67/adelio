<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlogCategories
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

        $create = ['blog.categories.index', 'blog.categories.create', 'blog.categories.store'];
        $update = ['blog.categories.update', 'blog.categories.edit'];
        $delete = ['blog.categories.destroy', 'blog.categories.massDestroy'];

        if (in_array($routeName, $create) && !auth()->user()->isAllowedTo('create-blog-category')) {
            return redirect()->route('index')->with('error', __('messages.generic.access_not_auth'));
        }

        if (in_array($routeName, $update) && !auth()->user()->isAllowedTo('update-blog-category')) {
            return redirect()->route('blog.categories.index')->with('error', __('messages.categories.edit_not_auth'));
        }

        if (in_array($routeName, $delete) && !auth()->user()->isAllowedTo('delete-blog-category')) {
            return redirect()->route('blog.categories.index')->with('error', __('messages.categories.delete_not_auth'));
        }

        return $next($request);
    }
}
