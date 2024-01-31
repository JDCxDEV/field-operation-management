@section('pageTitle', 'Activity Logs')

@section('breadcrumb')
	<li class="breadcrumb-item text-muted">
		<a href="/" class="text-muted text-hover-primary">Home</a>
	</li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>
	<li class="breadcrumb-item">
    <span class="text-primary">Activity Logs</span>		
  </li>	
@stop

<x-base-layout>
  <activity-logs-table
  fetch-url="{{ route('activity-logs.fetch') }}"
  current-mode="{{ Auth::user()->theme_mode }}"
  ></activity-logs-table>
</x-base-layout>