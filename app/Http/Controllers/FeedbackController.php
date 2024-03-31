<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Worker;
use function Symfony\Component\HttpFoundation\Session\Storage\Handler\commit;

class FeedbackController extends Controller
{
    public $worker_init;
    public function __construct()
    {

            $this->worker_init = new Worker();

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Feedback::with(['users_linked','comments.users_linked'])->orderBy('id','Desc')->paginate(10);
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

                'title' => 'required|max:100|string',
                'description' => 'required|string|max:500',
                'category' => 'required|in:'.implode(',',$this->worker_init->allowed_categories)

        ]);

        if($validate->fails()){
            return response(['message'=>$validate->errors()],422);
        }

        DB::beginTransaction();
        try {
            $feedback_builder = $request->only('title', 'description', 'category');
            $feedback_builder['created_by'] = auth()->user()->id;
            $feedback_created = Feedback::create($feedback_builder);
            DB::commit();
            $feedback_created->load('users_linked');
            $feedback_created->load('comments.users_linked');
            return response(['message' => 'Feedback Saved Successfully', 'data' => $feedback_created], 200);
        }
        catch (\Exception $exception){
            return response(['message' => $exception->getMessage()], 404);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Feedback $feedback)
    {
        $feedback->load('users_linked');
        $feedback->load('comments.users_linked');
        return $feedback;
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
