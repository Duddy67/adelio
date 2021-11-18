@extends ('layouts.admin')

@section ('main')
    <h3>@php echo (isset($menu)) ? __('labels.menus.edit_menu') : __('labels.menus.create_menu'); @endphp</h3>

    @php $action = (isset($menu)) ? route('admin.menus.menus.update', $query) : route('admin.menus.menus.store', $query) @endphp
    <form method="post" action="{{ $action }}" id="itemForm">
        @csrf

	@if (isset($menu))
	    @method('put')
	@endif

        @foreach ($fields as $field)
	    @php $value = (isset($menu)) ? old($field->name, $field->value) : old($field->name); @endphp
	    <x-input :field="$field" :value="$value" />
        @endforeach

	<input type="hidden" id="cancelEdit" value="{{ route('admin.menus.menus.cancel', $query) }}">
	<input type="hidden" id="close" name="_close" value="0">
    </form>
    <x-toolbar :items=$actions />

    @if (isset($menu))
	<form id="deleteItem" action="{{ route('admin.menus.menus.destroy', $query) }}" method="post">
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
