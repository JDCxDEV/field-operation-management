@section('pageTitle', 'FAQs')

@section('breadcrumb')
	<li class="breadcrumb-item text-muted">
		<a href="/" class="text-muted text-hover-primary">Home</a>
	</li>
  <li class="breadcrumb-item">
		<span class="bullet bg-gray-400 w-5px h-2px"></span>
  </li>
	<li class="breadcrumb-item">
    <span class="text-primary">FAQs</span>		
  </li>	
@stop

<x-base-layout>
  <div id="kt_app_content_container">
    <div class="card">
      <div class="card-body p-lg-15">
        <div class="mb-13">
          <div class="mb-15">
            <h4 class="fs-2x text-gray-800 w-bolder mb-6">                           
              Frequently Asked Questions              
            </h4>
          </div>
        </div>
        <div class="row mb-12">
          @foreach ($faqs as $faq)
            <div class="col-md-6 pe-md-10 mb-10 mb-md-10">
              <h2 class="text-gray-800 fw-bold mb-4">                           
                {{ $faq->title }}        
              </h2>
              <div class="m-0">
                @foreach ($faq->getItems() as $key => $item)
                  <div class="d-flex align-items-center collapsible py-3 {{ $key !== 0 ? 'collapsed' : '' }} toggle mb-0" data-bs-toggle="collapse" data-bs-target="#kt_item_{{ $item->id }}_{{ $key }}"> 
                    <div class="btn btn-sm btn-icon mw-20px btn-active-color-primary me-5">
                      <!--begin::Svg Icon | path: icons/duotune/general/gen036.svg-->
                      <span 
                      class="svg-icon toggle-on svg-icon-primary svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor"></rect>
                          <rect x="6.0104" y="10.9247" width="12" height="2" rx="1" fill="currentColor"></rect>
                        </svg>
                      </span>
                      <!--end::Svg Icon-->
                      <!--begin::Svg Icon | path: icons/duotune/general/gen035.svg-->
                      <span
                      class="svg-icon toggle-off svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor"></rect>
                          <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="currentColor"></rect>
                          <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="currentColor"></rect>
                        </svg>
                      </span>
                      <!--end::Svg Icon-->
                    </div>
                    <h4 class="text-gray-700 fw-bold cursor-pointer mb-0">                           
                      {{ $item->title }}                            
                    </h4>
                  </div>
                  <div id="kt_item_{{ $item->id }}_{{ $key }}" class="fs-6 ms-1 collapse {{ $key === 0 ? 'show' : '' }}">
                    <!--begin::Text-->
                    <div class="mb-4 text-gray-600 fw-semibold fs-6 ps-10">
                      {!! $item->content !!}            
                    </div>
                    <!--end::Text-->
                  </div>
                  @if(count($faq->getItems()) > ($key + 1))
                    <div class="separator separator-dashed"></div>
                  @endif
                @endforeach
              </div>
            </div>
          @endforeach
          
        </div>
      </div>
      {{-- <div class="accordion accordion-icon-toggle" id="kt_accordion_1">
        @foreach ($faqs as $key => $faq)
          <div class="accordion-item">
            <h2 class="accordion-header" id="kt_accordion_{{ $faq->id }}_header_{{ $faq->id }}">
              <button class="accordion-button fs-4 fw-semibold {{ $key === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_{{ $faq->id }}_body_{{ $faq->id }}" aria-expanded="true" aria-controls="kt_accordion_{{ $faq->id }}_header_{{ $faq->id }}">
                {{ $faq->title }}
              </button>
            </h2>
            <div id="kt_accordion_{{ $faq->id }}_body_{{ $faq->id }}" class="accordion-collapse collapse {{ $key === 0 ? 'show' : '' }}" aria-labelledby="kt_accordion_{{ $faq->id }}_header_{{ $faq->id }}" data-bs-parent="#kt_accordion_1">
              <div class="accordion-body">
                <div style="margin-left: 10px;">
                  @foreach ($faq->getItems() as $item)
                    <h5 class="text-gray-800">{{ $item->title }}</h5>
                    <span class="text-gray-800"><li>{{ $item->content }}</li></span>
                    <div class="mb-7"></div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div> --}}
    </div>
  </div>
</x-base-layout>