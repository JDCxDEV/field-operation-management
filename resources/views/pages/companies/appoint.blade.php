@section('pageTitle', 'Companies')

@section('breadcrumb')
	<li class="breadcrumb-item text-muted">
		<a href="/" class="text-muted text-hover-primary">Home</a>
	</li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>
  <li class="breadcrumb-item text-muted">
    <a href="{{ route("companies.index") }}" class="text-muted text-hover-primary">Companies</a>
  </li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>  
  <li class="breadcrumb-item">
    <span class="text-primary">Assign Coordinators</span>
  </li>
@stop

<x-base-layout>
  <company-appoint-coordinator 
  fetch-url="{{ route("companies.fetch") }}"
  />
</x-base-layout>