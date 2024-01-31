@section('pageTitle', 'Markets')

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
    <span class="text-primary">Assign Directors</span>
  </li>
@stop

<x-base-layout>
  <market-appoint-director 
  fetch-url="{{ route("markets.fetch") }}"
  />
</x-base-layout>