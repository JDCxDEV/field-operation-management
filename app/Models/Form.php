<?php

namespace App\Models;

use App\Models\User;
use App\Models\FormDependent;
use App\Models\Company;
use App\Models\Market;
use App\Traits\ActivityLogTrait;
use App\Helpers\GoogleStorageHelper;
use App\Helpers\ImageHelper;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Auth;
use Carbon\Carbon;

class Form extends Model
{
    use SoftDeletes;        
    use ActivityLogTrait;
    use Searchable;

    public $asYouType = true;


    const DRAFT = 0;
    const SUBMITTED = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'application_id' => $this->application_id,
            'client_name' => $this->renderClientName(),
            'client_email' => $this->client_email,
            'client_address_line_1' => $this->client_address_line_1,
            'client_city' => $this->client_city,
            'client_state' => $this->client_state,
            'agent_name' => $this->creator->getNameAttribute(),
            'market_name' => optional($this->market)->market_name,
            'company' => optional($this->company)->company,
            'status' => optional($this->form_status)->status
        ];
    }

    /**
     * The attributes that will be on the default result.
     *
     * @var array
     */
    protected $appends = [
        'name',
        'creator_name',
        'create_at_readable',
        'view_url',
        'update_url',
        'dependents_list'
    ];

    /**
     * Get a fullname combination of first_name and last_name
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return "{$this->client_first_name} {$this->client_last_name}";
    }

    /**
     * Get created by
     *
     * @return string
     */
    public function getCreatorNameAttribute()
    {
        return $this->creator->name;
    }

    /**
     * Get readable created_at
     *
     * @return string
     */
    public function getCreateAtReadableAttribute()
    {
        return $this->created_at->format('m/d/Y h:i:s A');
    }

    /**
     * Get view url
     *
     * @return string
     */
    public function getViewUrlAttribute()
    {
        return route("agent-submission-forms.view", $this->id);
    }

    /**
     * Get update url
     *
     * @return string
     */
    public function getUpdateUrlAttribute()
    {
        return route("agent-submission-forms.show", $this->id);
    }

    public function getDependentsListAttribute()
    {
        return $this->form_dependents;
    }

    public static function store($request, $id) 
    {
        if(!$id) {
            $request["created_by"] = Auth::user()->id;
            $request["application_id"] = Form::createApplicationId();
            $request["company_id"] = Auth::user()->getCompanyId();
            $request["market_id"] = Auth::user()->getMarketId();
            return Form::create($request);    
        } else {
            $form = Form::find($id);
            if(!$form->application_id) {
                $request["application_id"] = Form::createApplicationId($form->id);
            }
            if(isset($request["current_step"]) && $form->current_step >= $request["current_step"]) {
                $request["current_step"] = $form->current_step;
            }
            $form->update($request);
            return $form;
        }
    }

    public static function createApplicationId($id = null)
    {
        $max = Form::max("id");
        $applicationId = $id ? $id : ($max + 1);
        
        $idLength = strlen($applicationId);
        $pattern = "0000000";
        $patternLength = strlen($pattern);

        if($patternLength >= $idLength) {
            $pattern = substr($pattern, $patternLength - ($patternLength - $idLength));
        } else {
            $pattern = "";
        }

        $applicationId = $pattern . $applicationId;
        return $applicationId;
    }

    public static function storeWithFile($request, $id)
    {
        $form = Form::find($id);
        if(isset($request->file_id)) {
            FormFile::find($request->file_id)->update(["id_type" => $request->id_type]);
        } else {
            $extension = explode('/', mime_content_type($request->file))[1];
            $date = Carbon::now()->format("m-d-Y");
            
            $fileName = strtolower($form->application_id) . '-' . $request->id_type . '-' . $date . "-" . Str::random(5) . ".". $extension;

            $savePath = 'form-files/'. $form->id .'/'. $fileName;

            $file = $request->file;

            $file = str_replace('data:image/'. $extension .';base64,', '', $file);
            $file = str_replace(' ', '+', $file);

            $gcs = new GoogleStorageHelper;
            $gcs->storeFile(base64_decode($file), $savePath);

            $a = $form->form_files()->create([
                "file_type" => $request->id_type,
                "id_type" => $request->id_type,
                "file_path" => $savePath
            ]);
        }

        return $form;
    }

    public static function deleteFile($fileId)
    {
        $file = FormFile::findOrfail($fileId);
        if($file) {
            $gcs = new GoogleStorageHelper;
            $gcs->deleteFile($file->file_path);
            $file->delete();
            $form = Form::find($file->form_id);
        }
        return $form;
    }

    public static function storeSignature($request, $id) 
    {
        $form = Form::find($id);
        
        $payload = $request->all();
        $payload["signature_base64"] = $request->signature;
        $signature = $request->signature;

        $signature = str_replace('data:image/png;base64,', '', $signature);
        $signature = str_replace(' ', '+', $signature);
        $fileName = Str::random(10).'.'.'png';
        
        $savePath = "form-files/{$form->id}/signature-". $fileName;
        $payload["signature"] = $savePath;

        $gcs = new GoogleStorageHelper;
        $gcs->storeFile(base64_decode($signature), $savePath);

        if($form->signature) {
            $gcs->deleteFile($form->signature);
        }

        $form->update($payload);
        return $form;
    }

    public static function storeLeadFormPdf($pdf, $id) 
    {
        $form = Form::find($id);

        $fileName = Str::random(10).'.'.'pdf';
        $savePath = "form-files/{$form->id}/". $fileName;
        $payload["lead_form_pdf_path"] = $savePath;
        
        $gcs = new GoogleStorageHelper;
        $file = $gcs->storeFile($pdf, $savePath);

        if($form->lead_form_pdf_path) {
            $gcs->deleteFile($form->lead_form_pdf_path);
        }

        $form->update($payload);
        return $form;
    }

    public function memberCount() 
    {
        $count = 1;
        if($this->spousal_ssn) {
            $count = 2;
        }
        $count = $count + $this->form_dependents->count();
        return $count;
    }

    /**
     * belongs to relation of Form to User
     * foreignKey = created_by
     */
    public function creator() 
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function company() 
    {
        return $this->belongsTo(Company::class);
    }

    public function market() 
    {
        return $this->belongsTo(Market::class);
    }    

    public function form_status() 
    {
        return $this->belongsTo(Status::class, "status_id");
    }

    public function form_dependents() 
    {
        return $this->hasMany(FormDependent::class);
    }

    public function form_files() 
    {
        return $this->hasMany(FormFile::class);
    }

    public function renderClientName() 
    {
        return "{$this->client_first_name} {$this->client_last_name}";
    }

    public function renderClientPhone() 
    {
        if($this->client_phone) {
            $parts = sscanf($this->client_phone,'%2c%3c%3c%4c');
            return "($parts[1]) $parts[2]-$parts[3]";
        }

    }
}
