<?php

namespace Modules\Task\Services;

use Illuminate\Support\Facades\Auth;
use Modules\Task\Http\Requests\StoreTaskRequest;

class TaskService
{
    public function createTask(StoreTaskRequest $requestData)
    {
        return Auth::user()->tasks()->create($requestData->all());
    }
}
