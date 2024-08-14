<?php

namespace App\Http\Controllers;

use App\Services\AsanaService;
use Illuminate\Http\Request;

class AsanaTaskController extends Controller
{
    protected AsanaService $asanaService;

    public function __construct(AsanaService $asanaService)
    {
        $this->asanaService = $asanaService;
    }

    public function index($projectId)
    {
        $projectId='1208019516654154'; // from getProject api
        $tasks = $this->asanaService->getTasks($projectId);
        return response()->json($tasks);
    }

    public function store(Request $request, $projectId)
    {
        $projectId='1208019516654154'; // from getProject api
        $workspaceId='1207998698886218'; // from getWorkspace api
        $data = [
            'name' => 'My New Task',
            'resource_type' => 'task',
            'resource_subtype' => 'default_task',
            'notes' => 'This is a task created from the API.',
            'due_on' => '2024-08-10',
        ];
        $task = $this->asanaService->createTask($workspaceId,$projectId, $data);
        return response()->json($task);
    }

    public function update(Request $request, $taskId)
    {
        $taskId='1208020074929209'; // from getTask api
        $data = [
            'name' => 'My New Task',
            'resource_type' => 'task',
            'resource_subtype' => 'default_task',
            'notes' => 'This is a task updated from the API.',
            'due_on' => '2024-08-10',
        ];
        $task = $this->asanaService->updateTask($taskId, $data);
        return response()->json($task);
    }

    public function destroy($taskId)
    {
        $taskId='1208009506397831'; // from getTask api
        $this->asanaService->deleteTask($taskId);
        return response()->json(['message' => 'Task deleted successfully.']);
    }

    public function getWorkspace()
    {
        $workspaces = $this->asanaService->getWorkspaces();
        return response()->json($workspaces);
    }

    public function getProject($workspaceId)
    {
        $workspaceId='1207998698886218'; // from getWorkspace api
        $projects = $this->asanaService->getProjects($workspaceId);
        return response()->json($projects);
    }

    public function storeProject()
    {
        $workspaceId = '1207998698886218';
        $data = [
            'name' => 'Project Name',
            'notes' => 'Project Notes',
        ];

        $project = $this->asanaService->createProject($workspaceId, $data);
        return response()->json($project);
    }

    public function updateProject($projectId)
    {
        $data = [
            'name' => 'Updated Name',
            'notes' => 'Updated Notes',
        ];

        $project = $this->asanaService->updateProject($projectId, $data);
        return response()->json($project);
    }

    public function destroyProject($projectId)
    {
        $result = $this->asanaService->deleteProject($projectId);
        return response()->json($result);
    }

    public function showProject($projectId)
    {
        $project = $this->asanaService->getProject($projectId);
        return response()->json($project);
    }



}
