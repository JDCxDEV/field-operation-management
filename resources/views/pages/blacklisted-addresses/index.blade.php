@section('pageTitle', 'Blacklisted Addresses')

@section('breadcrumb')
	<li class="breadcrumb-item text-muted">
		<a href="/" class="text-muted text-hover-primary">Home</a>
	</li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>
	<li class="breadcrumb-item">
    <span class="text-primary">Blacklisted Addresses</span>		
  </li>	
@stop

<x-base-layout>
  <blacklisted-address-table 
	fetch-url="{{ route("blacklisted-addresses.fetch") }}"
	store-url="{{ route("blacklisted-addresses.store") }}"	
	></blacklisted-address-table>
</x-base-layout>