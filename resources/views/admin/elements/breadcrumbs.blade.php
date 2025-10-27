@if(!empty($breadCrumbs))
<nav aria-label="breadcrumb">
    <ol class="breadcrumb chain-breadcrump">
        @foreach ($breadCrumbs as $bread)
            @if($bread['st'])
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $bread['name'] }}</li>    
            @else
                <li class="breadcrumb-item"><a href="{{ $bread['url'] }}">{{ $bread['name'] }}</a></li>
            @endif
        @endforeach
    </ol>
</nav>
@endif
