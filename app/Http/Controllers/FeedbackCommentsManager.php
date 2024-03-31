<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\FeedbackComments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FeedbackCommentsManager extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validate = Validator::make($request->all(),[
            'feedback_id' => 'required|exists:feedback,id',
            'comments' => 'required|string|max:500',
        ]);

        if($validate->fails()){
            return response(['message'=>$validate->errors()],422);
        }

        DB::beginTransaction();
        try {
            $comment_builder = $request->only('feedback_id', 'comments');
            $comment_builder['comments'] = str_replace([PHP_EOL,'script'],' ',$comment_builder['comments']);
            $comment_builder['commented_by'] = auth()->user()->id;
            $comments_created = FeedbackComments::create($comment_builder);
            Feedback::find($request->feedback_id)->increment('comments_count');
            DB::commit();
            $comments_created->load('users_linked');
//            $feedback = Feedback::with(['users_linked','comments.users_linked'])->find($request->feedback_id);
            return response(['message' => 'Comments Saved Successfully', 'data' => $comments_created], 200);
        }
        catch (\Exception $exception){
            return response(['message' => $exception->getMessage()], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
