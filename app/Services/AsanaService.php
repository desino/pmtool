<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Helpers\ApiHelper;
use Illuminate\Support\Facades\Response;

class AsanaService
{
    private Client $client;

    public function __construct()
    {
        $key = "2/1206969167492969/1208008802985718:fb3401866e90c74deebdf582c71c00b3";
        $this->client = new Client([
            'verify' => "C:\wamp64\bin\php\php8.3.0\/extras\ssl\cacert-2024-03-11.pem",
            'base_uri' => 'https://app.asana.com/api/1.0/',
            'headers' => [
                'Authorization' => 'Bearer ' . $key,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * Retrieve projects for a specific workspace.
     *
     * @param string $workspaceId
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getProjects(string $workspaceId): JsonResponse
    {
        try {
            $response = $this->client->get("workspaces/{$workspaceId}/projects");
            $data = json_decode($response->getBody()->getContents(), true);
            return Response::json([true, 'Projects retrieved successfully.', $data]);
        } catch (ClientException $e) {
            Log::error('Asana API Error (Get Projects)', [
                'error' => $e->getMessage(),
                'response' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ]);
            return Response::json([false, 'Failed to retrieve projects.', [], $e->getCode()]);
        }
    }

    /**
     * Retrieve all workspaces.
     *
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getWorkspaces(): JsonResponse
    {
        try {
            $response = $this->client->get("workspaces");
            $data = json_decode($response->getBody()->getContents(), true);
            return Response::json([true, 'Workspaces retrieved successfully.', $data]);
        } catch (ClientException $e) {
            Log::error('Asana API Error (Get Workspaces)', [
                'error' => $e->getMessage(),
                'response' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ]);
            return Response::json([false, 'Failed to retrieve workspaces.', [], $e->getCode()]);
        }
    }

    /**
     * Retrieve tasks for a specific project.
     *
     * @param string $projectId
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getTasks(string $projectId): JsonResponse
    {
        try {
            $response = $this->client->get("projects/{$projectId}/tasks");
            $data = json_decode($response->getBody()->getContents(), true);
            return Response::json([true, 'Tasks retrieved successfully.', $data]);
        } catch (ClientException $e) {
            Log::error('Asana API Error (Get Tasks)', [
                'error' => $e->getMessage(),
                'response' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ]);
            return Response::json([false, 'Failed to retrieve tasks.', [], $e->getCode()]);
        }
    }

    /**
     * Create a new task in a specific workspace and project.
     *
     * @param string $workspaceId
     * @param string $projectId
     * @param array $data
     * @return JsonResponse
     */
    public function createTask(string $workspaceId, string $projectId, array $data): JsonResponse
    {
        $payload = [
            'data' => array_merge($data, [
                'workspace' => $workspaceId,
                'projects' => [$projectId],
            ])
        ];

        try {
            $response = $this->client->post("tasks", [
                'json' => $payload
            ]);
            $responseData = json_decode($response->getBody()->getContents(), true);
            return Response::json([true, 'Task created successfully.', $responseData]);
        } catch (Exception $e) {
            Log::error('Asana API Error (Create Task)', [
                'error' => $e->getMessage(),
                'response' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ]);
            return Response::json([false, 'Failed to create task.', [], $e->getCode()]);
        }
    }

    /**
     * Update an existing task.
     *
     * @param string $taskId
     * @param array $data
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function updateTask(string $taskId, array $data): JsonResponse
    {
        $payload = [
            'data' => $data
        ];

        try {
            $response = $this->client->put("tasks/{$taskId}", [
                'json' => $payload
            ]);
            $responseData = json_decode($response->getBody()->getContents(), true);
            return Response::json([true, 'Task updated successfully.', $responseData]);
        } catch (ClientException $e) {
            Log::error('Asana API Error (Update Task)', [
                'error' => $e->getMessage(),
                'response' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ]);
            return Response::json([false, 'Failed to update task.', [], $e->getCode()]);
        }
    }

    /**
     * Delete an existing task.
     *
     * @param string $taskId
     * @return JsonResponse
     */
    public function deleteTask(string $taskId): JsonResponse
    {
        try {
            $this->client->delete("tasks/{$taskId}");
            return Response::json([true, 'Task deleted successfully.', []]);
        } catch (ClientException $e) {
            Log::error('Asana API Error (Delete Task)', [
                'error' => $e->getMessage(),
                'response' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ]);
            return Response::json([false, 'Failed to delete task.', [], $e->getCode()]);
        }
    }

    /**
     * Create a new project in a specific workspace.
     *
     * @param string $workspaceId
     * @param array $data
     * @return JsonResponse
     */
    public function createProject(string $workspaceId, array $data): JsonResponse
    {
        $payload = [
            'data' => array_merge($data, [
                'workspace' => $workspaceId, // Required
            ])
        ];

        try {
            Log::info('Asana API Request Payload for Create Project:', $payload);

            $response = $this->client->post('projects', [
                'json' => $payload
            ]);
            $responseData = json_decode($response->getBody()->getContents(), true);
            return Response::json([true, 'Project created successfully.', $responseData]);
        } catch (ClientException $e) {
            Log::error('Asana API Error (Create Project)', [
                'error' => $e->getMessage(),
                'response' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ]);
            return Response::json([false, 'Failed to create project.', [], $e->getCode()]);
        }
    }

    /**
     * Update an existing project.
     *
     * @param string $projectId
     * @param array $data
     * @return JsonResponse
     */
    public function updateProject(string $projectId, array $data): JsonResponse
    {
        $payload = [
            'data' => $data
        ];

        try {
            Log::info('Asana API Request Payload for Update Project:', $payload);

            $response = $this->client->put("projects/{$projectId}", [
                'json' => $payload
            ]);
            $responseData = json_decode($response->getBody()->getContents(), true);
            return Response::json([true, 'Project updated successfully.', $responseData]);
        } catch (ClientException $e) {
            Log::error('Asana API Error (Update Project)', [
                'error' => $e->getMessage(),
                'response' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ]);
            return Response::json([false, 'Failed to update project.', [], $e->getCode()]);
        }
    }

    /**
     * Delete an existing project.
     *
     * @param string $projectId
     * @return JsonResponse
     */
    public function deleteProject(string $projectId): JsonResponse
    {
        try {
            $this->client->delete("projects/{$projectId}");
            return Response::json([true, 'Project deleted successfully.', []]);
        } catch (ClientException $e) {
            Log::error('Asana API Error (Delete Project)', [
                'error' => $e->getMessage(),
                'response' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ]);
            return Response::json([false, 'Failed to delete project.', [], $e->getCode()]);
        }
    }

    /**
     * Retrieve a specific project.
     *
     * @param string $projectId
     * @return JsonResponse
     */
    public function getProject(string $projectId): JsonResponse
    {
        try {
            $response = $this->client->get("projects/{$projectId}");
            $responseData = json_decode($response->getBody()->getContents(), true);
            return Response::json([true, 'Project retrieved successfully.', $responseData]);
        } catch (ClientException $e) {
            Log::error('Asana API Error (Get Project)', [
                'error' => $e->getMessage(),
                'response' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ]);
            return Response::json([false, 'Failed to retrieve project.', [], $e->getCode()]);
        }
    }
}
