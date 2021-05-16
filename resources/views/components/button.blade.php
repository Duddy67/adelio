@php $class = (array_key_exists($button->id, $btnClasses)) ? $btnClasses[$button->id] : 'btn-secondary' @endphp

@if (isset($button->class))
    @php $class = $button->class @endphp
@endif

@php $icon = (array_key_exists($button->id, $btnIcons)) ? 'fa '.$btnIcons[$button->id] : '' @endphp
@php $icon = (isset($button->icon)) ? $button->id : $icon @endphp

@if (!empty($icon))
    @php $icon = '<i class="'.$icon.'"></i>' @endphp
@endif

<button type="button" id="{{ $button->id }}" class="btn btn-space {{ $class }}">{!! $icon !!} {{ $button->label }}</button>
