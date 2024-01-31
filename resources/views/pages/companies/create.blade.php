@section('pageTitle', 'Add Companies')

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
    <span class="text-primary">Add Company</span>
  </li>
@stop

<x-base-layout>
  <div class="card">
    <div class="card-body">
      <company-view
      store-url="{{ route("companies.store") }}"
      ></company-view>
    </div>
  </div>
</x-base-layout>