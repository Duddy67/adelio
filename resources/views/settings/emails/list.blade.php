@extends ('layouts.admin')

@section ('header')
    <p class="h3">@php echo __('labels.title.emails'); @endphp</p>
@endsection

@section ('main')
    @superadmin ()
	<div class="card">
	    <div class="card-body">
            <x-toolbar :items=$actions />
	    </div>
	</div>
    @endsuperadmin

    <div class="card">
        <div class="card-body">
            <x-filters :filters="$filters" :url="$url" />
        </div>
    </div>

    @if (!empty($rows)) 
        <x-item-list :columns="$columns" :rows="$rows" :url="$url" />
    @else
        <div class="alert alert-info" role="alert">
            No item has been found.
        </div>
    @endif

    <x-pagination :items=$items />

    <input type="hidden" id="createItem" value="{{ route('settings.emails.create', $query) }}">
    <input type="hidden" id="destroyItems" value="{{ route('settings.emails.index', $query) }}">
    <input type="hidden" id="checkinItems" value="{{ route('settings.emails.massCheckIn', $query) }}">

    <form id="selectedItems" action="{{ route('settings.emails.index', $query) }}" method="post">
        @method('delete')
        @csrf
    </form>
@endsection

@push ('scripts')
    <script src="{{ asset('/js/admin/list.js') }}"></script>
@endpush
