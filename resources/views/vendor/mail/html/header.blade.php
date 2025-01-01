@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
{{--            @if (trim($slot) === 'Budgetku')--}}
{{--            <img src="https://www.budgetku.com/logo.svg" class="logo" alt="Logo">--}}
{{--            @else--}}
            <img src="https://www.budgetku.com/logo.svg" class="logo" alt="logo">
            {{-- {{ $slot }} --}}
{{--            @endif--}}
        </a>
    </td>
</tr>
