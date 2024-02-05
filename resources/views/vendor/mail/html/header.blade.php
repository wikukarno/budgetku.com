@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'WIKUKARNO.COM')
            <img src="https://api.wikukarno.com/apple-touch-icon.png" class="logo" alt="Logo">
            @else
            <img src="https://api.wikukarno.com/apple-touch-icon.png" class="logo" alt="logo">
            {{-- {{ $slot }} --}}
            @endif
        </a>
    </td>
</tr>
