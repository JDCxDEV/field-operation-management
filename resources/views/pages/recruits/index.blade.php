@section('pageTitle', 'Manage Recruits')

@section('breadcrumb')
	<li class="breadcrumb-item text-muted">
		<a href="/" class="text-muted text-hover-primary">Home</a>
	</li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>
	<li class="breadcrumb-item">
    <span class="text-primary">Recruits</span>		
  </li>	
@stop

<x-base-layout>
	<recruit-table 
	fetch-url="{{ route("recruits.fetch") }}"
	store-url="{{ route("recruits.store") }}"	
	companies-fetch-url="{{ route("companies.fetch") }}"
	markets-fetch-url="{{ route("markets.fetch") }}"
	></recruit-table>
</x-base-layout>