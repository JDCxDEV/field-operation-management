@section('pageTitle', 'Company Overview')

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
    <span class="text-primary">{{ $company->company }}</span>
  </li>
@stop

<x-base-layout>
  <company-overview
  users-fetch-url="{{ route("users.fetch") }}"
  companies-fetch-url="{{ route("companies.fetch", $company->id) }}"
	markets-store-url="{{ route("markets.store") }}"    
  markets-fetch-url="{{ route("companies.markets", $company->id) }}"
  company-find-url="{{ route("companies.find", $company->id) }}"
  ></company-overview>
</x-base-layout>