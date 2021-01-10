@component('mail::message')
# From: {{$data['name']}}
# Subject: {{$data['subject']}}

{!! $data['body'] !!}

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
