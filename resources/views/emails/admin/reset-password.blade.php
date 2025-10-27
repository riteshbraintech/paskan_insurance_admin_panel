@component('mail::message')
# Introduction

Welcome to eco-harmony

@component('mail::button', ['url' => $link, 'color' => 'success'])
Reset Password
@endcomponent



Thanks,<br>
{{ config('app.name') }}
@endcomponent
