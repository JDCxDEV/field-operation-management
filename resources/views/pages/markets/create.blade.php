@section('pageTitle', 'Add Market')

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
    <span class="text-primary">Add Market</span>
  </li>
@stop

<x-base-layout>
  <div class="card">
    <div class="card-body">
      <market-view
      store-url="{{ route("markets.store") }}"
      ></market-view>
    </div>
  </div>
</x-base-layout>