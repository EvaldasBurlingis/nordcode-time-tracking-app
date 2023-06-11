<?php

namespace Modules\Task\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Task\Exports\TasksExport;
use Modules\Task\Http\Requests\DownloadTaskFileRequest;
use Modules\Task\Http\Requests\StoreTaskRequest;
use Modules\Task\Services\TaskService;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $taskService)
    {
    }

    public function index(Request $request): InertiaResponse
    {
        $tasks = Auth::user()->tasks();

        if ($request->has('dateFrom')) {
            $tasks->dateBetween($request->get('dateFrom'), $request->get('dateTo', Carbon::now()));
        }

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks->paginate(20)->withQueryString(),
            'dateFrom' => $request->get('dateFrom', null),
            'dateTo' => $request->get('dateTo', null),
        ]);
    }

    public function store(StoreTaskRequest $request)
    {
        try {
            $this->taskService->createTask($request);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }


        return to_route('tasks.index');
    }

    public function download(DownloadTaskFileRequest $request)
    {
        return Excel::download(
            new TasksExport(
                $request->get('dateFrom'),
                $request->get('dateTo')),
            'tasks.' . $request->get('fileType')
        );
    }

}
