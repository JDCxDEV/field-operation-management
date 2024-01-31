<!--begin::Footer-->
<div id="kt_app_footer" class="app-footer">
	<!--begin::Footer container-->
	<div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
		<!--begin::Copyright-->
		<div class="footer d-flex text-dark order-2 order-md-1">
			<span class="me-1 text-dark">&copy; 2022-{{ now()->year }}</span>
			FieldOps – All rights reserved. Created by&nbsp;<a href="{{ env('APP_WEBSITE_LINK') }}" target="_blank" class="text-gray-800 text-hover-primary">{{ env('APP_CREATED_BY') }}</a>.
		</div>
		<!--end::Copyright-->
		<!--begin::Menu-->
		<ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
			<li class="menu-item">
				<span class="text-gray-800">Version {{ env("APP_VERSION") }}</span>
			</li>
		</ul>
		<!--end::Menu-->
	</div>
	<!--end::Footer container-->
</div>
<!--end::Footer-->
