<!DOCTYPE html>
<html>
<head>
	<title>Lead Form</title>
</head>
	<style>

	* {
	  font-family: "Roboto";
	}

	html {
	  margin: 0;
	}

	body {
    font-size: 12px;
    margin: 30px;
	}

	body, h1, h2, h3, h4, h5, h6, div {
    line-height: 1.1;
	}

  .page-break {
    page-break-after: always;
	}

	.padding-b-sm {
		padding-bottom: 5px;
	}

	.padding-t-lg {
		padding-top: 20px;
	}

  .text-center {
    text-align: "center";
  }

  .text-right {
    text-align: "right";
  }

	</style>

	<body>
    <div class="padding-b-sm" style="text-align:center">
			<h3><strong>{{ $template->title }}</strong></h3>
		</div>
    {!! $template->content !!}
    @if (count($template->children))
      @foreach ($template->children as $child)
        <div class="padding-b-sm" style="text-align:center">
          <h3><strong>{{ $child->title }}</strong></h3>
        </div>
        {!! $child->content !!}
      @endforeach
    @endif

    <div class="padding-t-lg">
      <div style="float:left; width: 40%;">
        <div>Device Used: {{ $form->device }}</div>
      </div>
      <div style="float:right; width: 40%;">
        <div style="width: 80%">
          <div style="text-align: center;">
            <img width="150px" height="140px" src="{{ $form->signature_base64 }}">
          </div>
          <div style="text-align: center">{{ $form->renderClientName() }}</div>
          <div style="height: 1px; background: #000"></div>
          <div style="text-align: center">Signature over printed name</div>
        </div>
      </div>
    </div>
	</body>
</html>