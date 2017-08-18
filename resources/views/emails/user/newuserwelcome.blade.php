@component('mail::message')
# Email Demo

Thanks for signing up.

@component('mail::button', ['url' => 'http://localhost:8000/home'])
  View My Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
