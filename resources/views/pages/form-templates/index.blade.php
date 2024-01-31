@section('pageTitle', 'Form Manager')

@section('breadcrumb')
	<li class="breadcrumb-item text-muted">
		<a href="/" class="text-muted text-hover-primary">Home</a>
	</li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>
	<li class="breadcrumb-item">
    <span class="text-primary">Form Manager</span>		
  </li>	
@stop

<x-base-layout>
	<form-template-overview
	address-fetch-url="{{ route("blacklisted-addresses.fetch") }}"
	address-store-url="{{ route("blacklisted-addresses.store") }}"
	status-fetch-url="{{ route("statuses.fetch") }}"
	status-store-url="{{ route("statuses.store") }}"	
	>
	</form-template-overview>
</x-base-layout>