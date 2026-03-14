<div class="container-fluid mt-4">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
        aria-label="breadcrumb" class="card">
        <ol class="breadcrumb align-self-baseline m-3">
            @php
                $replace_url = str_replace(url('/') . '/', '', url()->full() . '/');
                $replace_url = trim(str_replace('/', ' ', $replace_url));
            @endphp
            @if ($replace_url === '' ? 'active' : '')
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            @else
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                @foreach (explode(' ', $replace_url) as $index => $path)
                    @if ($loop->last)
                        <li class="breadcrumb-item text-capitalize active" aria-current="page">{{ $path }}</li>
                    @else
                        <li class="breadcrumb-item text-capitalize" aria-current="page"><a href="{{ route('dashboard.index') }}">{{ $path }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        </ol>
    </nav>
</div>
