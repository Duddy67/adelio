<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Users\Group;
use App\Traits\Admin\ItemConfig;


class GroupController extends Controller
{
    use ItemConfig;

    /*
     * Instance of the model.
     */
    protected $model;

    /*
     * Name of the model.
     */
    protected $modelName = 'group';

    /*
     * Name of the plugin.
     */
    protected $pluginName = 'users';


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin.users.groups');
	$this->model = new Group;
    }

    /**
     * Show the group list.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // Gather the needed data to build the item list.
        $columns = $this->getColumns();
        $actions = $this->getActions('list');
        $filters = $this->getFilters($request);
	$items = $this->model->getItems($request);
	$rows = $this->getRows($columns, $items);
	$query = $request->query();
	$url = ['route' => 'admin.users.groups', 'item_name' => 'group', 'query' => $query];

        return view('admin.users.groups.list', compact('items', 'columns', 'rows', 'actions', 'filters', 'url', 'query'));
    }

    /**
     * Show the form for creating a new group.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create(Request $request)
    {
        // Gather the needed data to build the form.
        $fields = $this->getFields();
        $actions = $this->getActions('form', ['destroy']);
	$query = $request->query();

        return view('admin.users.groups.form', compact('fields', 'actions', 'query'));
    }

    /**
     * Show the form for editing the specified group.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit(Request $request, $id)
    {
        // Gather the needed data to build the form.
        $group = Group::findOrFail($id);
        $fields = $this->getFields($group);
        $actions = $this->getActions('form');
	$query = $queryWithId = $request->query();
	$queryWithId['group'] = $id;

        return view('admin.users.groups.form', compact('group', 'fields', 'actions', 'query', 'queryWithId'));
    }

    /**
     * Update the specified group.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
	$group = Group::findOrFail($id);

        $this->validate($request, [
	    'name' => [
		'required',
		'regex:/^[a-z0-9-]{3,}$/',
		Rule::unique('groups')->ignore($id)
	    ],
	]);

	$group->name = $request->input('name');
	$group->description = $request->input('description');
	$group->save();

	$query = $request->query();

        if ($request->input('_close', null)) {
	    // Redirect to the list.
	    return redirect()->route('admin.users.groups.index', $query)->with('success', __('messages.groups.update_success'));
	}

	$query['group'] = $group->id;

	return redirect()->route('admin.users.groups.edit', $query)->with('success', __('messages.groups.update_success'));
    }

    /**
     * Store a new group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
	    'name' => [
		'required',
		'regex:/^[a-z0-9-]{3,}$/',
		'unique:groups'
	    ],
	]);

	$group = Group::create(['name' => $request->input('name'), 'description' => $request->input('description')]);
	$query = $request->query();

        if ($request->input('_close', null)) {
	    return redirect()->route('admin.users.groups.index', $query)->with('success', __('messages.groups.create_success'));
	}

	$query['group'] = $group->id;

	return redirect()->route('admin.users.groups.edit', $query)->with('success', __('messages.groups.create_success'));
    }

    /**
     * Remove the specified group from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
	$group = Group::findOrFail($id);
	$group->users()->detach();
	$name = $group->name;
	$group->delete();

	// Do not redirect during mass deletion.
	if (debug_backtrace()[1]['function'] == 'massDestroy') {
	    return;
	}

	return redirect()->route('admin.users.groups.index', $request->query())->with('success', __('messages.groups.delete_success', ['name' => $name]));
    }

    /**
     * Remove one or more groups from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function massDestroy(Request $request)
    {
        // Remove the groups selected from the list.
        foreach ($request->input('ids') as $id) {
	    $this->destroy($request, $id);
	}

	return redirect()->route('admin.users.groups.index', $request->query())->with('success', __('messages.groups.delete_list_success', ['number' => count($request->input('ids'))]));
    }
}
