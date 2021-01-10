@component('mail::message')
# From: {{$data['from']}}
# Subject: {{$data['subject']}}

{!! $data['body'] !!}

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
