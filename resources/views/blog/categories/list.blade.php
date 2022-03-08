@extends ('layouts.admin')

@section ('header')
    <p class="h3">Categories</p>
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

    <input type="hidden" id="createItem" value="{{ route('blog.categories.create', $query) }}">
    <input type="hidden" id="destroyItems" value="{{ route('blog.categories.index', $query) }}">
    <input type="hidden" id="checkinItems" value="{{ route('blog.categories.massCheckIn', $query) }}">
    <input type="hidden" id="publishItems" value="{{ route('blog.categories.massPublish', $query) }}">
    <input type="hidden" id="unpublishItems" value="{{ route('blog.categories.massUnpublish', $query) }}">

    <form id="selectedItems" action="{{ route('blog.categories.index', $query) }}" method="post">
        @method('delete')
        @csrf
    </form>
@endsection

@push ('scripts')
    <script src="{{ asset('/js/admin/list.js') }}"></script>
@endpush