<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Initiative;
use App\Models\Project;
use App\Models\Ticket;

class ProjectService
{


    public static function getInitiativeProjects($id)
    {
        $project = Project::where('initiative_id', $id)->where('status', 1)->get();
        return $project;
    }

    public static function createAndAssignProjectForTasks($request)
    {
        $project = Project::create(['name' => $request->project_name, 'initiative_id' => $request->initiative_id]);
        $request->merge(['project_id' => $project->id]);
        Self::assignProjectForTasks($request);
    }

    public static function assignProjectForTasks($request)
    {
        $projectId = $request->input('project_id');
        Ticket::whereIn('id', $request->input('selectedTasks'))->update(['project_id' => $projectId]);
    }

    public static function removeProjectForTasks($request)
    {
        Ticket::whereIn('id', $request->input('selectedTasks'))->update(['project_id' => null]);
    }
}
