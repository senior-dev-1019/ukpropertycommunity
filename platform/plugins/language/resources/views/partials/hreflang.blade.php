@foreach($supportedLocales as $localeCode => $properties)
    @if ($localeCode != Language::getCurrentLocale())
        <link rel="alternate" href="{{ Language::getLocalizedURL($localeCode, url()->current()) }}" hreflang="{{ $localeCode }}" />
    @endif
@endforeach
