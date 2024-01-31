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
    <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>  
    <li class="breadcrumb-item">
    <span class="text-primary">{{ $form->form_id }}</span>
  </li>
@stop

<x-base-layout>
  <div class="row mb-10">
    <div class="col text-right">
      <a href="{{ route("agent-submission-forms.show", $form->id) }}" class="btn btn-success">Edit</a>
    </div>
  </div>
  <div id="kt_app_content_container">
  <agent-submission-form 
    fetch-url="{{ isset($form) ? route("agent-submission-forms.find", $form->id) : "" }}"
    fetch-template-url="{{ route("form-templates.fetch-active") }}"
    viewing
    />
  </div>
</x-base-layout>