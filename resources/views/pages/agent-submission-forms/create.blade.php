@section('pageTitle', 'Agent Submission Forms')

@section('breadcrumb')
	<li class="breadcrumb-item text-muted">
		<a href="/" class="text-muted text-hover-primary">Home</a>
	</li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>
  <li class="breadcrumb-item text-muted">
    <a href="{{ route("agent-submission-forms.index") }}" class="text-muted text-hover-primary">Forms</a>
  </li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>  
    <li class="breadcrumb-item">
    <span class="text-primary">Agent Submission Forms</span>
  </li>
@stop

<x-base-layout>
  <div id="kt_app_content_container">
    <agent-submission-form 
    fetch-url="{{ isset($form) ? route("agent-submission-forms.find", $form->id) : "" }}"
    client-validation-url="{{ isset($form) ? route("agent-submission-forms.client-validation", $form->id) : route("agent-submission-forms.client-validation") }}"
    client-address-validation-url="{{ isset($form) ? route("agent-submission-forms.client-address-validation", $form->id) : route("agent-submission-forms.client-address-validation") }}"
    employment-data-url="{{ isset($form) ? route("agent-submission-forms.employment-data", $form->id) : route("agent-submission-forms.employment-data") }}"
    enrollment-data-url="{{ isset($form) ? route("agent-submission-forms.enrollment-data", $form->id) : "" }}"
    additional-files-url="{{ isset($form) ? route("agent-submission-forms.additional-files", $form->id) : "" }}"
    plan-information-url="{{ isset($form) ? route("agent-submission-forms.plan-information", $form->id) : "" }}"
    payment-information-url="{{ isset($form) ? route("agent-submission-forms.payment-information", $form->id) : "" }}"
    save-signature-url="{{ isset($form) ? route("agent-submission-forms.save-signature", $form->id) : "" }}"
    submit-url="{{ isset($form) ? route("agent-submission-forms.submit", $form->id) : "" }}"
    fetch-template-url="{{ route("form-templates.fetch-active") }}"
    :enable-watcher="false"
    />
  </div>
</x-base-layout>