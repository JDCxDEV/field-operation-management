@section('pageTitle', $pageTitle)

@section('breadcrumb')
	<li class="breadcrumb-item text-muted">
		<a href="/" class="text-muted text-hover-primary">Home</a>
	</li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>
	<li class="breadcrumb-item">
    <span class="text-primary">Markets</span>		
  </li>	
@stop
<x-base-layout>
	<market-table 
	fetch-url="{{ route("markets.fetch") }}"
	store-url="{{ route("markets.store") }}"
	company-id="{{ auth()->user()->companyCoordinator() ? auth()->user()->getCompanyId() : "" }}"
	companies-fetch-url="{{ route("companies.fetch") }}"	
	markets-fetch-url="{{ route("markets.fetch") }}"
	company-coordinator="{{ auth()->user()->companyCoordinator() }}"
	company-id="{{ auth()->user()->companyCoordinator() || auth()->user()->marketDirector() ? auth()->user()->getCompanyId() : "" }}"
	market-director="{{ auth()->user()->marketDirector() }}"
	market-id="{{ auth()->user()->marketDirector() ? auth()->user()->getMarketId() : "" }}"
	></market-table>
</x-base-layout>