@extends ('layouts.admin')

@section ('main')
    <h3>@php echo (isset($group)) ? __('labels.groups.edit_group') : __('labels.groups.create_group'); @endphp</h3>

    @php $action = (isset($group)) ? route('admin.users.groups.update', $query) : route('admin.users.groups.store', $query) @endphp
    <form method="post" action="{{ $action }}" id="itemForm">
        @csrf

	@if (isset($group))
	    @method('put')
	@endif

        @foreach ($fields as $field)
	    @php $value = (isset($group)) ? old($field->name, $field->value) : old($field->name); @endphp
	    <x-input :field="$field" :value="$value" />
        @endforeach

	<input type="hidden" id="cancelEdit" value="{{ route('admin.users.groups.cancel', $query) }}">
	<input type="hidden" id="close" name="_close" value="0">
    </form>
    <x-toolbar :items=$actions />

    @if (isset($group))
	<form id="deleteItem" action="{{ route('admin.users.groups.destroy', $query) }}" method="post">
	    @method('delete')
	    @csrf
	</form>
    @endif
@endsection

@push ('style')
    <link rel="stylesheet" href="{{ asset('/vendor/adminlte/plugins/jquery-ui/jquery-ui.min.css') }}"></script>
@endpush

@push ('scripts')
    <script type="text/javascript" src="{{ asset('/vendor/adminlte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/vendor/tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/admin/datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/admin/form.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/admin/disable.toolbars.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/tinymce/filemanager.js') }}"></script>
@endpush
