<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Users\RoleController;
use App\Http\Controllers\Users\PermissionController;
use App\Http\Controllers\Users\GroupController;
use App\Http\Controllers\Settings\GeneralController;
use App\Http\Controllers\Settings\EmailController;
use App\Http\Controllers\Cms\FileManagerController;
use App\Http\Controllers\Cms\FileController;
use App\Http\Controllers\Blog\PostController;
use App\Http\Controllers\Blog\CategoryController as BlogCategoryController;
use App\Http\Controllers\Blog\SettingController as BlogSettingController;
use App\Http\Controllers\Admin\Menus\MenuController;
use App\Http\Controllers\Admin\Menus\MenuItemController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\Api\TokenController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', [SiteController::class, 'index'])->name('site.index');

Route::get('/post/{id}/{slug}', [PostController::class, 'show'])->name('blog.post');
Route::get('/category/{id}/{slug}', [BlogCategoryController::class, 'index'])->name('blog.category');

Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/profile/token', [TokenController::class, 'update'])->name('profile.token');

Route::get('/cms/filemanager', [FileManagerController::class, 'index'])->name('cms.filemanager.index');
Route::post('/cms/filemanager', [FileManagerController::class, 'upload']);
Route::delete('/cms/filemanager', [FileManagerController::class, 'destroy'])->name('cms.filemanager.destroy');

Route::get('/expired', function () {
    return view('cms.filemanager.expired');
})->name('expired');

Route::middleware(['guest'])->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
});

Route::prefix('users')->group(function () {
	// Users
	Route::controller(UserController::class)->group(function () {
		Route::delete('/users', 'massDestroy')->name('users.users.massDestroy');
		Route::get('/users/batch', 'batch')->name('users.users.batch');
		Route::put('/users/batch', 'massUpdate')->name('users.users.massUpdate');
		Route::get('/users/cancel/{user?}', 'cancel')->name('users.users.cancel');
		Route::put('/users/checkin', 'massCheckIn')->name('users.users.massCheckIn');
		Route::get('/users', 'index')->name('users.users');
		Route::resource('users', UserController::class, ['as' => 'users'])->except(['show']);

	});

	// Roles
	Route::controller(RoleController::class)->group(function () {
		Route::delete('/roles', 'massDestroy')->name('users.roles.massDestroy');
		Route::get('/roles/cancel/{role?}', 'cancel')->name('users.roles.cancel');
		Route::put('/roles/checkin', 'massCheckIn')->name('users.roles.massCheckIn');
		Route::resource('roles', RoleController::class, ['as' => 'users'])->except(['show']);
	});

	// Groups
	Route::controller(GroupController::class)->group(function () {
	    Route::delete('/groups', 'massDestroy')->name('users.groups.massDestroy');
	    Route::get('/groups/batch', 'batch')->name('users.groups.batch');
	    Route::put('/groups/batch', 'massUpdate')->name('users.groups.massUpdate');
	    Route::get('/groups/cancel/{group?}', 'cancel')->name('users.groups.cancel');
	    Route::put('/groups/checkin', 'massCheckIn')->name('users.groups.massCheckIn');
	    Route::resource('groups', GroupController::class, ['as' => 'users'])->except(['show']);
	});

	// Permissions
	Route::controller(PermissionController::class)->group(function () {
	    Route::get('/permissions', 'index')->name('users.permissions.index');
	    Route::patch('/permissions', 'build')->name('users.permissions.build');
	    Route::put('/permissions', 'rebuild')->name('users.permissions.rebuild');
	});
});

Route::prefix('blog')->group(function () {
	// Posts 
	Route::controller(PostController::class)->group(function () {
	    Route::delete('/posts', 'massDestroy')->name('blog.posts.massDestroy');
	    Route::get('/posts/batch', 'batch')->name('blog.posts.batch');
	    Route::put('/posts/batch', 'massUpdate')->name('blog.posts.massUpdate');
	    Route::get('/posts/cancel/{post?}', 'cancel')->name('blog.posts.cancel');
	    Route::put('/posts/checkin', 'massCheckIn')->name('blog.posts.massCheckIn');
	    Route::put('/posts/publish', 'massPublish')->name('blog.posts.massPublish');
	    Route::put('/posts/unpublish', 'massUnpublish')->name('blog.posts.massUnpublish');
	    Route::get('/posts/{post}/edit/{tab?}', 'edit')->name('blog.posts.edit');
	    Route::resource('posts', PostController::class, ['as' => 'blog'])->except(['show', 'edit']);
	});

    // Categories
	Route::controller(BlogCategoryController::class)->group(function () {
        Route::delete('/categories', 'massDestroy')->name('blog.categories.massDestroy');
        Route::get('/categories/cancel/{category?}', 'cancel')->name('blog.categories.cancel');
        Route::put('/categories/checkin', 'massCheckIn')->name('blog.categories.massCheckIn');
        Route::put('/categories/publish', 'massPublish')->name('blog.categories.massPublish');
        Route::put('/categories/unpublish', 'massUnpublish')->name('blog.categories.massUnpublish');
        Route::get('/categories/{category}/up', 'up')->name('blog.categories.up');
        Route::get('/categories/{category}/down', 'down')->name('blog.categories.down');
        Route::get('/categories/{category}/edit/{tab?}', 'edit')->name('blog.categories.edit');
        Route::resource('categories', BlogCategoryController::class, ['as' => 'blog'])->except(['show', 'edit']);
	});

    // Settings
    Route::get('/settings/{tab?}', [BlogSettingController::class, 'index'])->name('blog.settings.index');
    Route::patch('/settings', [BlogSettingController::class, 'update'])->name('blog.settings.update');
});

Route::prefix('settings')->group(function () {
    // General settings
    Route::get('/general', [GeneralController::class, 'index'])->name('settings.general.index');
    Route::patch('/general', [GeneralController::class, 'update'])->name('settings.general.update');

    // Emails
	Route::controller(EmailController::class)->group(function () {
        Route::delete('/emails', 'massDestroy')->name('settings.emails.massDestroy');
        Route::get('/emails/cancel/{email?}', 'cancel')->name('settings.emails.cancel');
        Route::put('/emails/checkin', 'massCheckIn')->name('settings.emails.massCheckIn');
        Route::resource('emails', EmailController::class, ['as' => 'settings'])->except(['show']);
	});
});

Route::prefix('admin')->group(function () {

    Route::middleware(['admin'])->group(function () {
	Route::get('/', [AdminController::class, 'index'])->name('admin');

	// Files
	Route::get('/files', [FileController::class, 'index'])->name('admin.files.index');
	Route::delete('/files', [FileController::class, 'massDestroy'])->name('admin.files.massDestroy');
	Route::get('/files/batch', [FileController::class, 'batch'])->name('admin.files.batch');
	Route::put('/files/batch', [FileController::class, 'massUpdate'])->name('admin.files.massUpdate');

	Route::prefix('menus')->group(function () {
	    // Menus 
	    Route::delete('/menus', [MenuController::class, 'massDestroy'])->name('admin.menus.menus.massDestroy');
	    Route::get('/menus/cancel/{menu?}', [MenuController::class, 'cancel'])->name('admin.menus.menus.cancel');
	    Route::put('/menus/checkin', [MenuController::class, 'massCheckIn'])->name('admin.menus.menus.massCheckIn');
	    Route::put('/menus/publish', [MenuController::class, 'massPublish'])->name('admin.menus.menus.massPublish');
	    Route::put('/menus/unpublish', [MenuController::class, 'massUnpublish'])->name('admin.menus.menus.massUnpublish');
	    Route::resource('menus', MenuController::class, ['as' => 'admin.menus'])->except(['show']);
	    // Menu Items
	    Route::delete('/{code}/menuitems', [MenuItemController::class, 'massDestroy'])->name('admin.menus.menuitems.massDestroy');
	    Route::get('/{code}/menuitems/cancel/{menuItem?}', [MenuItemController::class, 'cancel'])->name('admin.menus.menuitems.cancel');
	    Route::put('/{code}/menuitems/checkin', [MenuItemController::class, 'massCheckIn'])->name('admin.menus.menuitems.massCheckIn');
	    Route::put('/{code}/menuitems/publish', [MenuItemController::class, 'massPublish'])->name('admin.menus.menuitems.massPublish');
	    Route::put('/{code}/menuitems/unpublish', [MenuItemController::class, 'massUnpublish'])->name('admin.menus.menuitems.massUnpublish');
	    Route::get('/{code}/menuitems/{menuItem}/up', [MenuItemController::class, 'up'])->name('admin.menus.menuitems.up');
	    Route::get('/{code}/menuitems/{menuItem}/down', [MenuItemController::class, 'down'])->name('admin.menus.menuitems.down');
	    Route::get('/{code}/menuitems', [MenuItemController::class, 'index'])->name('admin.menus.menuitems.index');
	    Route::get('/{code}/menuitems/create', [MenuItemController::class, 'create'])->name('admin.menus.menuitems.create');
	    Route::get('/{code}/menuitems/{menuItem}/edit', [MenuItemController::class, 'edit'])->name('admin.menus.menuitems.edit');
	    Route::post('/{code}/menuitems', [MenuItemController::class, 'store'])->name('admin.menus.menuitems.store');
	    Route::put('/{code}/menuitems/{menuItem}', [MenuItemController::class, 'update'])->name('admin.menus.menuitems.update');
	    Route::delete('/{code}/menuitems/{menuItem}', [MenuItemController::class, 'destroy'])->name('admin.menus.menuitems.destroy');
	});

	/*Route::prefix('settings')->group(function () {
	    Route::get('/general', [GeneralController::class, 'index'])->name('admin.settings.general.index');
	    Route::patch('/general', [GeneralController::class, 'update'])->name('admin.settings.general.update');
	    // Emails
	    Route::delete('/emails', [EmailController::class, 'massDestroy'])->name('admin.settings.emails.massDestroy');
	    Route::get('/emails/cancel/{email?}', [EmailController::class, 'cancel'])->name('admin.settings.emails.cancel');
	    Route::put('/emails/checkin', [EmailController::class, 'massCheckIn'])->name('admin.settings.emails.massCheckIn');
	    Route::resource('emails', EmailController::class, ['as' => 'admin.settings'])->except(['show']);
	});*/
    });
});

