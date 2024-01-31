@section('pageTitle', 'Dashboard')

@section('breadcrumb')
	<li class="breadcrumb-item text-muted">
		<a href="/" class="text-muted text-hover-primary">Home</a>
	</li>
@stop

<x-base-layout>
	<div>
		@if (auth()->user()->isVerified())
			<div class="card" style="width: 18rem;">
				<div class="card-body">
					<h5 class="card-title">Welcome back!</h5>
					<p class="card-text"><b>User: </b>{{ Auth::user()->name }}</p>
					<p class="card-text"><b>Role: </b>
						<span class="{{ Auth::user()->renderRole()["class"] }} fw-bold">{{ Auth::user()->renderRole()["description"] }}</div>
					</p>
				</div>
			</div>
		@else
			<verify-account></verify-account>			
		@endif
	</div>
</x-base-layout>
