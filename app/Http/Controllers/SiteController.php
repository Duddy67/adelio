<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog\Category;
use App\Models\Blog\Setting;


class SiteController extends Controller
{
    public function index(Request $request)
    {
        $page = 'site.home';
	$posts = null;
	$settings = [];

	if ($category = Category::where('slug', 'foo-bar')->first()) {
	    $posts = $category->getPosts($request);

	    $globalSettings = Setting::getDataByGroup('category');

	    foreach ($category->settings as $key => $value) {
		if ($value == 'global_setting') {
		    $settings[$key] = $globalSettings[$key];
		}
		else {
		    $settings[$key] = $category->settings[$key];
		}
	    }
	}

	$query = $request->query();

        return view('default', compact('page', 'category', 'settings', 'posts', 'query'));
    }
}
