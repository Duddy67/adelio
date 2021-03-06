@extends ('layouts.admin')

@section ('header')
    <p class="h3">Users</p>
@endsection

@section ('main')
    <div class="card">
	<div class="card-body">
	    <x-toolbar :items=$actions />
	</div>
    </div>

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

    <x-pagination :items="$items" />

    <input type="hidden" id="createItem" value="{{ route('users.users.create', $query) }}">
    <input type="hidden" id="destroyItems" value="{{ route('users.users.index', $query) }}">
    <input type="hidden" id="checkinItems" value="{{ route('users.users.massCheckIn', $query) }}">

    <form id="selectedItems" action="{{ route('users.users.index', $query) }}" method="post">
	@method('delete')
	@csrf
    </form>

    <div id="batch-window" class="modal">
	<div class="modal-content">
	    <iframe src="{{ route('users.users.batch', $query) }}" id="batchIframe" name="batch"></iframe>
	</div>
    </div>
@endsection

@push ('scripts')
    <script type="text/javascript" src="{{ url('/') }}/js/admin/list.js"></script>
@endpush
