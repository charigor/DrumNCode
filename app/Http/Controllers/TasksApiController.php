<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatusEnum;
use App\Http\Requests\TaskIndexRequest;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Http\Services\TaskService;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TasksApiController extends Controller
{



    public function __construct(protected TaskService $service)
    {

    }
    /**
     * Display a listing of the resource.
     */

    public function index(TaskIndexRequest $request)
    {

       return  $this->service->getItems($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskStoreRequest $request)
    {
        abort_unless($request->parent_id != null ? auth('sanctum')->user()->isOwnedTask($request->parent_id) : true,Response::HTTP_FORBIDDEN);
        $data =  $this->service->storeItem($request);
        return TaskResource::make($data)->additional(['message' => 'Task was created successfully'])
                                        ->response()
                                        ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        abort_unless(auth('sanctum')->user()->isOwnedTask($id),Response::HTTP_FORBIDDEN);

        $data =  $this->service->showItem($id);

        return TaskResource::make($data->load(['children']))->response()
                                                          ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskUpdateRequest $request, string $id)
    {
        abort_unless(auth('sanctum')->user()->isOwnedTask($id),Response::HTTP_FORBIDDEN);

        if($this->service->checkStatuses($id,TaskStatusEnum::Todo,true) && $request->status === TaskStatusEnum::Done->value){
            return response()->json(['message' => 'Nested task(s) has todo status'])->setStatusCode(Response::HTTP_FORBIDDEN);;
        }

        $data =  $this->service->updateItem($request,$id);

        return TaskResource::make($data->load('children'))->additional(['message' => 'Task was updated successfully'])
                                                          ->response()
                                                          ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        abort_unless(auth('sanctum')->user()->isOwnedTask($id),Response::HTTP_FORBIDDEN);

        if($this->service->checkStatuses($id,TaskStatusEnum::Done)){
            return response()->json(['message' => 'Current task or nested task(s) has done status'])->setStatusCode(Response::HTTP_FORBIDDEN);;
        }
        $this->service->deleteItem($id);

        return  response()->json(['message' => 'Task was deleted successfully'])->setStatusCode(Response::HTTP_OK);

    }


}
