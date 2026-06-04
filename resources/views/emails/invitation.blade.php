<x-mail::message>
# {{ trans('You have been invited!') }}

{{ trans('You have been invited to join :team on :app.', ['team' => $teamName, 'app' => config('app.name')]) }}

{{ trans('Click the button below to accept the invitation and get started.') }}

<x-mail::button :url="$url">
{{ trans('Accept Invitation') }}
</x-mail::button>

{{ trans("If you did not expect to receive this invitation, you can safely ignore this email.") }}

{{ trans('Thanks,') }}<br>
{{ config('app.name') }}

<x-slot:subcopy>
{{ trans("If you're having trouble clicking the \":actionText\" button, copy and paste the URL below into your web browser:", ['actionText' => trans('Accept Invitation')]) }}
<span class="break-all">[{{ $url }}]({{ $url }})</span>
</x-slot:subcopy>
</x-mail::message>
