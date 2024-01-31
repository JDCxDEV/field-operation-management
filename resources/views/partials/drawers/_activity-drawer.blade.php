<?php
	$drawerDirection = isset($params['drawer-direction']) ? $params['drawer-direction'] : 'end';
?>
<!--begin::Activities drawer-->
<recent-logs
fetch-url="{{ route("activity-logs.recent") }}"
drawer-direction="{{ $drawerDirection }}"
media-path-url={{ theme()->getMediaUrlPath() }}
activity-logs-url="{{ auth()->user()->superAdmin() ? route("activity-logs.index") : "" }}"
></recent-logs>
<!--end::Activities drawer-->
