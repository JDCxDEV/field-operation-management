@section('pageTitle', 'FAQ Content Management')

@section('breadcrumb')
	<li class="breadcrumb-item text-muted">
		<a href="/" class="text-muted text-hover-primary">Home</a>
	</li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>
	<li class="breadcrumb-item">
    <span class="text-primary">FAQ Content Management</span>		
  </li>	
@stop

<x-base-layout>
  <topic-table
	fetch-url="{{ route("topics.fetch") }}"
	store-url="{{ route("topics.store") }}"	
	></topic-table>
</x-base-layout>