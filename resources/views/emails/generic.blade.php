@component('mail::message')
# {{ $title }}

{{ $message }}

@if(!empty($data['order_id']))
@component('mail::button', ['url' => url("/orders/{$data['order_id']}")])
View Order
@endcomponent
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent