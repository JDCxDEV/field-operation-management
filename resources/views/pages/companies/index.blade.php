@section('pageTitle', 'Manage Companies')

@section('breadcrumb')
	<li class="breadcrumb-item text-muted">
		<a href="/" class="text-muted text-hover-primary">Home</a>
	</li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>
	<li class="breadcrumb-item">
    <span class="text-primary">Companies</span>		
  </li>	
@stop

<x-base-layout>
	<company-table 
	fetch-url="{{ route("companies.fetch") }}"
	store-url="{{ route("companies.store") }}"	
	></company-table>
</x-base-layout>