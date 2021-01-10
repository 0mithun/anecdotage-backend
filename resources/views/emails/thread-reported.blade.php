@component('mail::message')
# Hi anecdotage Admin
{{$reason}}


Please
[review]({{ $url }}) this item


@component('mail::button', ['url' => $url])
Review
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent