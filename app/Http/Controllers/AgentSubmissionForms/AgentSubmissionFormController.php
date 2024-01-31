<?php

namespace App\Http\Controllers\AgentSubmissionForms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FormExport;
use App\Http\Requests\Forms\ClientValidationRequest;
use App\Http\Requests\Forms\ClientAddressValidationRequest;
use App\Http\Requests\Forms\EnrollmentDataRequest;
use App\Http\Requests\Forms\AdditionalFilesRequest;
use App\Http\Requests\Forms\EmploymentDataRequest;
use App\Http\Requests\Forms\PlanInformationRequest;
use App\Http\Requests\Forms\PaymentInformationRequest;
use App\Http\Requests\Forms\SignatureRequest;
use App\Http\Requests\Forms\SpousalRequest;
use App\Http\Requests\Forms\DependentRequest;
use App\Models\Form;
use App\Models\FormTemplate;
use App\Models\FormDependent;
use App\Models\BlacklistedAddress;
use App\Helpers\GoogleStorageHelper;
use App\Helpers\TwilioHelper;
use App\Helpers\SSNCheckerHelper;
use App\Helpers\AddressCheckerHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Jenssegers\Agent\Facades\Agent;
use Carbon\Carbon;
use Auth;
use DB;

class AgentSubmissionFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("pages.agent-submission-forms.index");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request)
    {   
        
        $forms = $this->filterForms($request);

        return $forms->orderBy("created_at", "desc")->paginate(10)
                ->through(function($form) {
                    $form->current_status = optional($form->form_status)->status;
                    $form->company_name = $form->company ? $form->company->company : "N/A";
                    $form->market_name = $form->market ? $form->market->market_name : "N/A";
                    $form->member_count = $form->memberCount();
                    return $form;
                });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("pages.agent-submission-forms.create");
    }

    public function find($id)
    {
        $gcs = new GoogleStorageHelper;

        $form = Form::findOrFail($id);
        $form = $this->renderFiles($form);
        
        return response()->json([
            "status" => 200,
            "form" => $form
        ]);
    }

    public function clientValidation(ClientValidationRequest $request, $id = null) 
    {
        /** Validated phone number */ 
        $twilio = new TwilioHelper;
        if($twilio->validatePhoneNumber($request->client_phone)["status"] == "invalid") {
            throw ValidationException::withMessages([
                'client_phone' => "Invalid client phone number",
            ]);   
        }

        // $ssnChecker = new SSNCheckerHelper;
        // $params = [
        //     "firstName" => $request->client_first_name,
        //     "lastName" => $request->client_last_name,
        //     "phone" => $request->client_phone,
        //     "ssn" => $request->client_ssn,
        //     "dob" => $request->client_dob 
        // ];
        // if($ssnChecker->checkSSN($params)["status"] == "invalid") {
        //     throw ValidationException::withMessages([
        //         'client_ssn' => "Invalid client SSN",
        //         'errors' => $ssnChecker->checkSSN($params)["errors"]
        //     ]);
        // }

        $form = "";
        if($id) {
            DB::beginTransaction();
                $payload = $request->all();
                $form = Form::store($payload, $id);
            DB::commit();        
        }

        return response()->json([
            "status" => 200,
            "message" => "Client validated",
            "form" => $form,
        ]);

    }

    public function clientAddressValidation(ClientAddressValidationRequest $request, $id = null) 
    {

        $addressChecker = new AddressCheckerHelper;
        $payload["address_type"] = $addressChecker->checkAddress($request->client_address_line_1, $request->client_city, $request->client_state, $request->client_zip);

        if($payload["address_type"] != "Residential") {
            return response()->json([
                "status" => 422,
                "address_type_error" => true,
                "message" => "Client's address is not a residential area."
            ]);            
        }


        /**
         * Check if address is blacklisted
         */
        if(!$request->ignore_warning) {
            $blacklisted = BlacklistedAddress::where(["state" => $request->client_state, "city" => $request->client_city, "zip" => $request->client_zip])->get();
            if(count($blacklisted)) {
                foreach($blacklisted as $address) {
                    similar_text($address->address_1, $request->client_address_line_1, $percent);
                    if($percent >= 90) {
                        return response()->json([
                            "status" => 422,
                            "percent_match" => $percent,
                            "message" => "Client address might be in the blacklisted"
                        ]);
                    }
                }
            }
        }

        $form = "";
        if($id) {
            DB::beginTransaction();
                $payload = $request->except(['ignore_warning']);
                $form = Form::store($payload, $id);
            DB::commit();        
        }

        return response()->json([
            "status" => 200,
            "message" => "Client address validated"
        ]);

    }

    public function employmentData(EmploymentDataRequest $request, $id = null)
    {
        $payload = $request->all();
        
        $addressChecker = new AddressCheckerHelper;
        $payload["address_type"] = $addressChecker->checkAddress($request->client_address_line_1, $request->client_city, $request->client_state, $request->client_zip);

        DB::beginTransaction();
            $payload["current_step"] = "2";
            $form = Form::store($payload, $id);
        DB::commit();
        
        $form = $this->renderFiles($form);

        return response()->json([
            "status" => 200,
            "redirect" => $id ? "" : route("agent-submission-forms.show", $form->id),
            "form" => $form,
        ]);
    }

    public function updateDependent(DependentRequest $request, $id = null) 
    {
        $form = Form::find($request->form_id);
        $dependent = "";
        
        if(!$id) {
            $dependent = FormDependent::create($request->all());
        } else {
            $dependent = FormDependent::find($id);
            $dependent->update(
                        $request->only([
                            "dependent_first_name", 
                            "dependent_last_name", 
                            "dependent_sex", 
                            "dependent_dob", 
                            "dependent_covered" ,
                            "dependent_coverage_type"
                        ])
                    );
        }

        return response()->json([
            "status" => 200,
            "form" => $form,
            "dependent" => $dependent
        ]);
    }

    public function deleteDependent($id) 
    {
        $dependent = FormDependent::find($id);
        $formId = $dependent->form_id;
        if($dependent) {
            $dependent->delete();
        }

        $form = Form::find($formId);

        return response()->json([
            "status" => 200,
            "form" => $form,
        ]);
    }

    public function enrollmentData(Request $request, $id = null)
    {

        if($request->store_toggles) {
            DB::beginTransaction();
                $payload = $request->except(["store_toggles"]);
                if(!$request->married && !$request->dependents) {
                    $payload["current_step"] = "3";
                }            
                $form = Form::store($payload, $id);
            DB::commit();
        } else if($request->store_spousal) {
            app(SpousalRequest::class);
            DB::beginTransaction();
                $payload = $request->except(["store_spousal"]);
                if(!$request->dependents) {
                    $payload["current_step"] = "3";
                }            
                $form = Form::store($payload, $id);
            DB::commit();
        } else if ($request->store_all) {
            if($request->married) {
                app(SpousalRequest::class);
            }
            DB::beginTransaction();
                $payload = $request->except(["store_all"]);
                $payload["current_step"] = "3";
                $form = Form::store($payload, $id);
            DB::commit();
        }

        $form = $this->renderFiles($form);

        return response()->json([
            "status" => 200,
            "form" => $form
        ]);
    }

    public function additionalFiles(Request $request, $id)
    {
        $validated = $request->validate([
            'file' => 'required',
            'id_type' => 'required',
        ]);

        DB::beginTransaction();
            $form = Form::storeWithFile($request, $id);
        DB::commit();

        $form = $this->renderFiles($form);

        return response()->json([
            "status" => 200,
            "form" => $form
        ]);
    }

    public function deleteFile($fileId)
    {
        DB::beginTransaction();
            $form = Form::deleteFile($fileId);
        DB::commit();

        $form = $this->renderFiles($form);

        return response()->json([
            "status" => 200,
            "form" => $form
        ]);
    }

    public function planInformation(Request $request, $id)
    {
        DB::beginTransaction();
            $payload = $request->all();
            $payload["current_step"] = "4";
            $form = Form::store($payload, $id);
        DB::commit();

        $form = $this->renderFiles($form);

        return response()->json([
            "status" => 200,
            "form" => $form
        ]);
    }

    public function paymentInformation(PaymentInformationRequest $request, $id)
    {
        DB::beginTransaction();
            $payload = $request->all();
            $payload["current_step"] = "6";
            $form = Form::store($payload, $id);
        DB::commit();

        $form = $this->renderFiles($form);

        return response()->json([
            "status" => 200,
            "form" => $form
        ]);
    }
    
    public function saveSignature(SignatureRequest $request, $id)
    {
        DB::beginTransaction();
            $form = Form::storeSignature($request, $id);
        DB::commit();

        $form = $this->renderFiles($form);

        return response()->json([
            "status" => 200,
            "form" => $form
        ]);
    }

    public function submit(Request $request, $id)
    {           
        
        $template = FormTemplate::where(["is_active" => true])->first();
        if($template) {
            $template->children = FormTemplate::where(["parent_id" => $template->id])->get();
        }        

        DB::beginTransaction();
            $form = Form::find($id);
            $payload = $request->all();
            $payload["status"] = Form::SUBMITTED;
            $device = Agent::platform() . ' - ' . Agent::browser();
            $payload["device"] = $device;
            $form->update($payload);
        DB::commit();

        $pdf = Pdf::loadView('pdfs.form', [
            "template" => $template,
            "form" => $form
        ]);
        $pdf->setPaper('A4', 'portrait');
        $form = Form::storeLeadFormPdf($pdf->download()->getOriginalContent(), $form->id);

        $form = $this->renderFiles($form);

        return response()->json([
            "status" => 200,
            "form" => $form,
            "redirect" => route("agent-submission-forms.create"),
        ]);
    }

    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $form = Form::find($id);
        return view("pages.agent-submission-forms.create", [
            "form" => $form,
        ]);
    }

   /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $form = Form::find($id);
        return view("pages.agent-submission-forms.view", [
            "form" => $form,
        ]);
    }

    public function saveChanges(Request $request, $id) 
    {
        $form = Form::find($id);
        $params = $request->all();
        app(ClientValidationRequest::class);
        app(ClientAddressValidationRequest::class);

        $addressChecker = new AddressCheckerHelper;
        $params["address_type"] = $addressChecker->checkAddress($request->client_address_line_1, $request->client_city, $request->client_state, $request->client_zip);
        if($params["address_type"] != "Residential") {
            return response()->json([
                "status" => 422,
                "address_type_error" => true,
                "message" => "Client's address is not a residential area."
            ]);            
        }
        app(EmploymentDataRequest::class);
        if($request->married) {
            app(SpousalRequest::class);
        }
        $params["current_step"] = 4;
        $form->update($params);
        return response()->json([
            "status" => 200,
            "message" => "Form has been updated"
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $form = Form::find($id);

        $form->update(["status_id" => $request->status_id]);
        return response()->json([
            "status" => 200,
            "message" => "Form has been updated"
        ]);
    }

    public function export(Request $request, $type) 
    {
        $forms = $this->filterForms($request);
        $forms = $forms->orderBy("created_at", "desc")->get();

        if($type == "xlsx" || $type == "csv") {
            return Excel::download(new FormExport($forms), "forms.{$type}");
        } else if($type == "pdf") {
            $pdf = Pdf::loadView('exports.forms-pdf', [
                "forms" => $forms
            ]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download()->getOriginalContent();
        }

    }

    public function filterForms($request) 
    {
        $user = Auth::user();
        $role = $user->renderRole()["description"];

        $forms = new Form;
        
        if($role != "Super Admin") {
            switch($role) {
                case "Agent":
                    $forms = $forms->where(["created_by" => $user->id]);
                    break;
                case "Coordinator":
                case "Coordinator & Market Director":
                    $userIds = $user->info->company->users->pluck("id")->toArray();
                    $forms = $forms->whereIn("created_by", $userIds);
                    break;
                case "Market Director":
                    $userIds = $user->info->market->users->pluck("id")->toArray();
                    $forms = $forms->whereIn("created_by", $userIds);
                    break;
                case "Pod Leader":
                    $userIds = $user->pod_users->pluck("id")->toArray();
                    $forms = $forms->whereIn("created_by", array_merge($userIds, [$user->id]));
                    break;
            }
        }

        if($request->search && $request->search != "") {
            $forms = $forms->search($request->search);
        }

        if($request->created_at && $request->created_at != "") {
            $dates = explode(" - ", $request->created_at);
            $start = Carbon::parse($dates[0])->startOfDay();
            $end = Carbon::parse($dates[1])->endOfDay();
            $forms = $forms->whereBetween('created_at', [$start, $end]);
        }

        if($request->state && ($request->state != "" && $request->state != "all")) {
            $forms = $forms->where(['client_state' => $request->state]);
        }

        if($request->status && $request->status != "all") {
            $status = $request->status == "submitted" ? FORM::SUBMITTED : FORM::DRAFT; 
            $forms = $forms->where(["status" => $status]);
        }

        if($request->form_status && $request->form_status != "all") {
            if($request->form_status == "draft") {
                $forms = $forms->where(["status_id" => null]);
            } else {
                $forms = $forms->where(["status_id" => $request->form_status]);
            }
        }

        if($request->company && $request->company != "all") {
            $forms = $forms->where(["company_id" => $request->company]);
        }

        if($request->market && $request->market != "all") {
            $forms = $forms->where(["market_id" => $request->market]);
        }        

        return $forms;
    }

    public function renderFiles($form) 
    {
        $gcs = new GoogleStorageHelper;
        $form->files = $form->form_files->map(function($file) use ($gcs) {
            $file->file_path = $gcs->renderFileUrl($file->file_path);
            return $file;
        });
        $form->signature_url = $gcs->renderFileUrl($form->signature);
        $form->lead_form_pdf_path_url = $gcs->renderPdfUrl($form->lead_form_pdf_path);

        return $form;
    }
}
