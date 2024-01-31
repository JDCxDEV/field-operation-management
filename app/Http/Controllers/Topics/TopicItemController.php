<?php

namespace App\Http\Controllers\Topics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Topics\TopicItemRequest;
use App\Models\TopicItem;

class TopicItemController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TopicItemRequest $request, $topic)
    {
        $order = TopicItem::where(["topic_id" => $topic])->max("order");
        $params = $request->only(["title", "content"]);
        $params["topic_id"] = $topic;
        $params["order"] = $order + 1;

        $item = TopicItem::create($params);

        return response()->json([
            "status" => 200,
            "topic_item" => $item 
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TopicItemRequest $request, $id)
    {
        $topic_item = TopicItem::withTrashed()->findOrFail($id);
        if($topic_item) {
            $topic_item->update($request->only(["title", "content"]));
        }

        return response()->json([
            "status" => 200,
            "topic_item" => $topic_item
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
        $topic_item = TopicItem::findOrFail($id);
        if($topic_item) {
            $topic_item->delete();
        }

        return response()->json([
            "status" => 200,
            "message" => "Topic item has been archived"
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
        $topic_item = TopicItem::onlyTrashed()->findOrFail($id);
        if($topic_item) {
            $topic_item->restore();
        }

        return response()->json([
            "status" => 200,
            "message" => "Topic item has been restored"
        ]);
    }

    public function updateOrder(Request $request, $topic) 
    {
        $items = $request->order;
        foreach($items as $item) {
            TopicItem::where(["topic_id" => $topic, "id" => $item["id"]])->update(["order" => $item["order"]]);      
        }

        return response()->json([
            "status" => 200,
            "message" => "Topic item order has been updated"
        ]);        
    }
}
