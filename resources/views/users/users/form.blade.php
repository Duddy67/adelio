@extends ('layouts.admin')

@section ('main')

    <h3>@php echo (isset($user)) ? __('labels.users.edit_user') : __('labels.users.create_user'); @endphp</h3>

    @php $action = (isset($user)) ? route('users.users.update', $query) : route('users.users.store', $query) @endphp

    @if (isset($user) && $photo) 
        <img src="{{ url('/').$photo->getThumbnailUrl() }}" >
    @endif

    <form method="post" action="{{ $action }}" id="itemForm" enctype="multipart/form-data">
        @csrf

	@if (isset($user))
	    @method('put')
	@endif

        @foreach ($fields as $field)
	    @php if (isset($user)) { 
		     $value = old($field->name, $field->value);
                     // The current user is editing their own account or the role is private.
                     if ($field->name == 'role' && (auth()->user()->id == $user->id || $user->isRolePrivate())) {
                         $field->extra = ['disabled'];
                     }
		 }
		 else {
                     if ($field->name == 'created_at' || $field->name == 'updated_at') {
                         continue;
                     }

		     $value = old($field->name);
		 }
	    @endphp

	    <div class="form-group">
		<x-input :field="$field" :value="$value" />
	    </div>
        @endforeach

	<input type="hidden" id="cancelEdit" value="{{ route('users.users.cancel', $query) }}">
	<input type="hidden" id="close" name="_close" value="0">
    </form>

    <div class="form-group">
	<x-toolbar :items=$actions />
    </div>

    @if (isset($user))
	<form id="deleteItem" action="{{ route('users.users.destroy', $query) }}" method="post">
	    @method('delete')
	    @csrf
	</form>
    @endif
@endsection

@push ('style')
    <link rel="stylesheet" href="{{ asset('/vendor/adminlte/plugins/daterangepicker/daterangepicker.css') }}">
@endpush

@push ('scripts')
    <script type="text/javascript" src="{{ asset('/vendor/adminlte/plugins/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/vendor/adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/admin/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/admin/form.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/admin/set.private.groups.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/admin/disable.toolbars.js') }}"></script>
@endpush

