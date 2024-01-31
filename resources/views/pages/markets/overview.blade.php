@section('pageTitle', 'Market Overview')

@section('breadcrumb')
	<li class="breadcrumb-item text-muted">
		<a href="/" class="text-muted text-hover-primary">Home</a>
	</li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>
  <li class="breadcrumb-item text-muted">
    <a href="{{ route("markets.index") }}" class="text-muted text-hover-primary">Markets</a>
  </li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>  
  <li class="breadcrumb-item">
    <span class="text-primary">{{ $market->market_name }}</span>
  </li>
@stop

<x-base-layout>
  <market-overview
  users-fetch-url="{{ route("users.fetch") }}"
  markets-fetch-url="{{ route("markets.fetch", $market->id) }}"
  market-find-url="{{ route("markets.find", $market->id) }}"
  ></market-overview>
</x-base-layout>