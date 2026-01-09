<x-mail::message>
# @lang('You have been invited to') {{ $teamName }}

@lang('Join the team by clicking on the button below or by copying the link into your browser.')

<x-mail::button :url="$url">
    @lang('Join :team', ['team' => $teamName])
</x-mail::button>

[{{ $url }}]({{ $url }})

{{ config('app.name') }}
</x-mail::message>