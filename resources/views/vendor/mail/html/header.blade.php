@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
            <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
            @else
            <img src="https://api-wikukarno.wikukarno.id/public/assets/logo/logo.png" class="logo" alt="logo">
            {{-- {{ $slot }} --}}
            @endif
        </a>
    </td>
</tr>