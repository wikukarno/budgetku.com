@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'BudgetKu')
                @php
                    $logoPath = public_path('v2/images/logo.svg');
                    $logoUrl = file_exists($logoPath) && app()->environment(['local', 'testing']) 
                        ? 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($logoPath))
                        : config('app.url') . '/v2/images/logo.svg';
                @endphp
                <img src="{{ $logoUrl }}" class="logo" alt="BudgetKu Logo" style="max-height: 50px; width: auto;">
            @else
                @php
                    $logoPath = public_path('v2/images/logo.svg');
                    $logoUrl = file_exists($logoPath) && app()->environment(['local', 'testing']) 
                        ? 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($logoPath))
                        : config('app.url') . '/v2/images/logo.svg';
                @endphp
                <img src="{{ $logoUrl }}" class="logo" alt="BudgetKu Logo" style="max-height: 50px; width: auto;">
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
