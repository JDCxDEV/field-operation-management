@section('pageTitle', $pageTitle)

@section('breadcrumb')
	<li class="breadcrumb-item text-muted">
		<a href="/" class="text-muted text-hover-primary">Home</a>
	</li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>
	<li class="breadcrumb-item">
    <span class="text-primary">User Accounts</span>		
  </li>	
@stop


<x-base-layout>
	<user-table 
	fetch-url="{{ route("users.fetch") }}"
	store-url="{{ route("users.store") }}"	
	companies-fetch-url="{{ route("companies.fetch") }}"
	markets-fetch-url="{{ route("markets.fetch") }}"
	company-coordinator="{{ auth()->user()->companyCoordinator() }}"
	company-id="{{ auth()->user()->companyCoordinator() || auth()->user()->marketDirector() || auth()->user()->podLeader() ? auth()->user()->getCompanyId() : "" }}"
	market-director="{{ auth()->user()->marketDirector() || auth()->user()->podLeader() }}"
	market-id="{{ auth()->user()->marketDirector() || auth()->user()->podLeader() ? auth()->user()->getMarketId() : "" }}"
	blank-image="{{ asset(theme()->getMediaUrlPath().'avatars/blank.png') }}"
	>
</x-base-layout>
