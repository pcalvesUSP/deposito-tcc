<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<img src="{{ $url.'/vendor/laravel-usp-theme/eeusp/images/LogoEE.jpg' }}" class="logo" alt="Logotipo da EEUSP">
@endif
</a>
<div>{{ $slot }}</div>
</td>
</tr>
