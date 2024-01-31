<?php

namespace App\Http\Controllers\FormTemplates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\FormTemplates\FormTemplateRequest;
use App\Models\FormTemplate;

class FormTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("pages.form-templates.index");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request)
    {   
        $templates = new FormTemplate;
        if($request->status) {
            if($request->status == "archived") {
                $templates = $templates->onlyTrashed();
            } 

            if($request->status == "all") {
                $templates = $templates->withTrashed();
            }
        }
        
        if($request->search && $request->search != null) {
            $templates = $templates->whereRaw("title like '%" . $request->search . "%' ");
        }

        return $templates->where(["parent_id" => null])->orderBy("is_active", "desc")->paginate(6);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FormTemplateRequest $request)
    {
        $count = FormTemplate::count();
        $is_active = $count === 0 ? true: false;
        $params = $request->only(["title", "description", "content"]);
        $params["is_active"] = $is_active;

        $template = FormTemplate::create($params);
        
        if(count($request->children)) {
            foreach($request->children as $child) {
                $child["parent_id"] = $template->id;
                FormTemplate::create($child);
            }
        }

        return response()->json([
            "status" => 200,
            "template" => $template 
        ]);
    }

    /**
     * Fetch the active resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchActive()
    {
        $template = FormTemplate::where(["is_active" => true])->first();
        if($template) {
            $template->children = FormTemplate::where(["parent_id" => $template->id])->get();
        }
        return response()->json([
            "status" => 200,
            "template" => $template
        ]);
    }

    /**
     * Find the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function find($id)
    {
        $template = FormTemplate::withTrashed()->find($id);
        $template->children = FormTemplate::where(["parent_id" => $template->id])->get();
        return response()->json([
            "status" => 200,
            "template" => $template
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FormTemplateRequest $request, $id)
    {
        $template = FormTemplate::withTrashed()->findOrFail($id);
        if($template) {
            $template->update($request->only(["title", "description", "content"]));
        }

        if(count($request->children)) {
            foreach($request->children as $child) {
                if(isset($child["id"])) {
                    FormTemplate::where(["id" => $child["id"]])->update($child);                    
                } else {
                    $child["parent_id"] = $template->id;
                    FormTemplate::create($child);
                }
            }
        }

        if(count($request->deleted)) {        
            FormTemplate::whereIn("id", $request->deleted)->delete();
        }
        return response()->json([
            "status" => 200,
            "template" => $template
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $template = FormTemplate::findOrFail($id);
        if($template) {
            $template->delete();
        }

        return response()->json([
            "status" => 200,
            "message" => "Form Template has been archived"
        ]);

    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $template = FormTemplate::onlyTrashed()->findOrFail($id);
        if($template) {
            $template->restore();
        }

        return response()->json([
            "status" => 200,
            "message" => "Form Template has been restored"
        ]);
    }

    /**
     * Activate the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activate($id)
    {
        $currentActive = FormTemplate::where(["is_active" => true])->first();
        if($currentActive) {
            $currentActive->update(["is_active" => false]);
        }

        $template = FormTemplate::find($id);
        if($template) {
            $template->update(["is_active" => true]);
        }

        return response()->json([
            "status" => 200,
            "message" => "Form Template has been set to active",
            "template" => $template
        ]);
    }
}
