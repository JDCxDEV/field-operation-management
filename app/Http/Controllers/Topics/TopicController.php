<?php

namespace App\Http\Controllers\Topics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Topics\TopicRequest;
use App\Models\Topic;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("pages.topics.index");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function faqs()
    {
        $faqs = Topic::orderBy("order", "asc")->get();
        return view("pages.faqs.index", [
            "faqs" => $faqs
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request)
    {   
        $topic = new Topic;
        if($request->status) {
            if($request->status == "archived") {
                $topic = $topic->onlyTrashed();
            } 

            if($request->status == "all") {
                $topic = $topic->withTrashed();
            }
        }
        
        if($request->search && $request->search != null) {
            $topic = $topic->whereRaw("title like '%" . $request->search . "%' ");
        }

        return $topic->orderBy("order", "asc")->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TopicRequest $request)
    {   
        $order = Topic::max("order");
        $order = $order + 1;
        $params = $request->only(["title"]);
        $params["order"] = $order;
        $topic = Topic::create($params);

        return response()->json([
            "status" => 200,
            "topic" => $topic 
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
        $topic = Topic::withTrashed()->find($id);
        $topic->items = $topic->items()->orderBy("order", "asc")->get();
        return response()->json([
            "status" => 200,
            "topic" => $topic
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TopicRequest $request, $id)
    {
        $topic = Topic::withTrashed()->findOrFail($id);
        if($topic) {
            $topic->update($request->only(["title"]));
        }

        return response()->json([
            "status" => 200,
            "topic" => $topic
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
        $topic = Topic::findOrFail($id);
        if($topic) {
            $topic->update(["order" => 0]);
            $topic->delete();
        }

        return response()->json([
            "status" => 200,
            "message" => "Topic has been archived"
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
        $topic = Topic::onlyTrashed()->findOrFail($id);
        $order = Topic::max("order");
        $order = $order + 1;

        if($topic) {
            $topic->update(["order" => $order]);
            $topic->restore();
        }

        return response()->json([
            "status" => 200,
            "message" => "Topic has been restored"
        ]);
    }

    public function updateOrder(Request $request) 
    {
        $items = $request->order;
        foreach($items as $item) {
            Topic::where(["id" => $item["id"]])->update(["order" => $item["order"]]);      
        }

        return response()->json([
            "status" => 200,
            "message" => "Topic order has been updated"
        ]);        
    }    
}
