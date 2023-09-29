<?php

namespace App\Http\Services;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;


class TaskService
{
    public function getItems($request):array
    {
        return $this->tree(Task::query()
                    ->with(['children'])
//                    ->whereHas('user',fn($q) => $q->where('user_id',$request->user()->id))//uncomment if nedd only user's tasks
                    ->filter($request->all())
                    ->get()
                    ->toArray());

    }
    public function storeItem($request)
    {
        $user = User::findOrFail($request->user()->id);
        return $user->tasks()->create($request->validated());

    }
    public function showItem($id)
    {
       return Task::findOrFail($id);

    }
    public function updateItem($request,$id)
    {
        $task = Task::findOrFail($id);
        $task->update($this->prepareCompletedData($task,$request->validated()));
        return $task->refresh();

    }
    public function deleteItem($id)
    {
        $task = Task::with('children')->findOrFail($id);
        $ids = $task->getNestedField();
        return $task->whereIn('id', $ids)->delete();
    }





    public function checkStatuses($id,$status,$exceptParent = null)
    {
        $task = Task::with('children')->findOrFail($id);
        $statuses = $task->getNestedField('status',$exceptParent);

        return in_array($status,$statuses);
    }
    public function tree($data): array
    {
        $tree = function ($elements, $parentId = 0) use (&$tree) {
            $branch = array();
            foreach ($elements as $element) {

                if ($element['parent_id'] == $parentId) {

                    $children = $tree($elements, $element['id']);
                    if ($children) {
                        $element['children'] = $children;
                    }  else {
                        $element['children'] = [];
                    }
                    $branch[] = $element;
                }

            }

            return $branch;
        };

        return $tree($data);
    }

    public function prepareCompletedData($model,$data)
    {
        if ($data['status'] !== $model->status->value) {
            $data['completed_at'] = $model->status->value !== TaskStatusEnum::Done->value ? now() : null;
        }
        return $data;
    }
}
