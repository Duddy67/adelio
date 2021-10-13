<h3 class="pb-2">{{ $category->name }}</h3>

<div class="card">
    <div class="card-body">
	@include('partials.filters')
    </div>
</div>

<ul class="post-list pt-4">
    @if (count($posts))
	@foreach ($posts as $post)
	    @include ('partials.blog.post')
        @endforeach
    @else
        <div>No post</div>
    @endif
</ul>

<x-pagination :items=$posts />

@push ('scripts')
    <script type="text/javascript" src="{{ $public }}/js/blog/category.js"></script>
@endpush