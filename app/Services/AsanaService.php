<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AsanaService
{
    private Client $client;
    private $returnData = [];
    private $workspaceId = "";
    private $response = "";
    public function __construct()
    {
        $this->workspaceId = Config::get('myapp.asana_workspace_id');
        $key = Config::get('myapp.asana_workspace_key');
        $verify = [];
        if (Config::get('myapp.ssl_certificate') != '') {
            $verify = [
                'verify' => Config::get('myapp.ssl_certificate')
            ];
        }

        $this->client = new Client($verify + [
            'base_uri' => Config::get('myapp.asana_workspace_base_uri'),
            'headers' => [
                'Authorization' => 'Bearer ' . $key,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function getProjects($workspaceId)
    {
        $response = $this->client->get("workspaces/{$workspaceId}/projects");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @throws GuzzleException
     */
    public function getWorkspaces()
    {
        $response = $this->client->get("workspaces");
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getTasks($projectId)
    {
        $response = $this->client->get("projects/{$projectId}/tasks");
        return json_decode($response->getBody()->getContents(), true);
    }

    public function createTask($projectId, $data)
    {
        $isErrorFromAsana = false;
        $payload = [
            'data' => array_merge($data, [
                'workspace' => $this->workspaceId,
                'projects' => [$projectId],
            ])
        ];


        try {
            $this->response = $this->client->post("tasks", [
                'json' => $payload
            ]);
        } catch (Exception $e) {
            Log::error('Asana API Error', [
                'error' => $e->getMessage(),
            ]);
            $isErrorFromAsana = true;
        }
        return $this->parsResponse($this->response, isError: $isErrorFromAsana);
    }

    /**
     * @throws GuzzleException
     */
    public function updateTask($taskId, $data)
    {

        $isErrorFromAsana = false;
        try {
            $payload = [
                'data' => $data
            ];

            $this->response = $this->client->put("tasks/{$taskId}", [
                'json' => $payload
            ]);
        } catch (ClientException $e) {
            Log::error('Asana API Error', [
                'error' => $e->getMessage(),
            ]);
            $isErrorFromAsana = true;
        }
        return $this->parsResponse($this->response, isError: $isErrorFromAsana);
    }

    public function deleteTask($taskId)
    {
        $isErrorFromAsana = false;
        try {
            $this->response = $this->client->delete("tasks/{$taskId}");
        } catch (ClientException $e) {
            Log::error('Asana API Error', [
                'error' => $e->getMessage(),
            ]);
            $isErrorFromAsana = true;
        }
        return $this->parsResponse($this->response, isError: $isErrorFromAsana);
    }

    public function createProject($data)
    {
        $isErrorFromAsana = false;
        try {
            $payload = [
                'data' => array_merge($data, [
                    'workspace' => $this->workspaceId, // Required
                ])
            ];

            $this->response = $this->client->post('projects', [
                'json' => $payload
            ]);
        } catch (ClientException $e) {
            Log::error('Asana API Error', [
                'error' => $e->getMessage(),
            ]);
            $isErrorFromAsana = true;
        }
        return $this->parsResponse($this->response, isError: $isErrorFromAsana);
    }

    public function updateProject($projectId, $data)
    {
        $isErrorFromAsana = false;
        try {
            $payload = [
                'data' => $data
            ];
            $this->response = $this->client->put("projects/{$projectId}", [
                'json' => $payload
            ]);
        } catch (ClientException $e) {
            Log::error('Asana API Error', [
                'error' => $e->getMessage(),
                'response' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ]);
            $isErrorFromAsana = false;
        }
        return $this->parsResponse($this->response, isError: $isErrorFromAsana);
    }

    public function deleteProject($projectId)
    {
        try {
            $response = $this->client->delete("projects/{$projectId}");

            return ['status' => 'success', 'message' => 'Project deleted successfully'];
        } catch (ClientException $e) {
            Log::error('Asana API Error', [
                'error' => $e->getMessage(),
                'response' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ]);
            return ['error' => $e->getMessage(), 'response' => json_decode($e->getResponse()->getBody()->getContents(), true)];
        }
    }

    public function getProject($projectId)
    {
        $isErrorFromAsana = false;
        try {
            $this->response = $this->client->get("projects/{$projectId}");
        } catch (ClientException $e) {
            $isErrorFromAsana = true;
            Log::error('Asana API Error', [
                'error' => $e->getMessage(),
                'response' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ]);
        }
        return $this->parsResponse($this->response, isError: $isErrorFromAsana);
    }

    private function parsResponse($data, $isError = false)
    {
        $this->returnData['data'] = $data != "" ? json_decode($data->getBody()->getContents(), true) : '';
        $this->returnData['error_status'] = $isError;

        return $this->returnData;
    }
}
