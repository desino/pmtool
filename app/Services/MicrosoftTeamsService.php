<?php

namespace App\Services;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Notifications\Action;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class MicrosoftTeamsService
{
    /**
     * Send a notification to a user in Microsoft Teams.
     *
     * This method sends the payload to the recipient users' Microsoft Teams instance.
     *
     * @param Ticket $ticket The ticket related to the notification.
     * @param User $actionByUser The user who performed the action.
     * @param object $actionData Data related to the action.
     * @param Collection $recipientUsers The users to receive the notification.
     */
    public static function sendNotification(Ticket $ticket, User $actionByUser, object $actionData, Collection $recipientUsers)
    {
        $payload = self::preparePayload(ticket: $ticket, actionByUser: $actionByUser, actionData: $actionData);
        $recipientUsers->each(function ($eachRecipientUser) use ($payload) {
            Http::post($eachRecipientUser->teams_webhook_url, $payload);
        });
    }

    /**
     * Prepare the payload for sending a notification to Microsoft Teams.
     *
     * This method constructs an adaptive card payload for a given ticket and action.
     * It includes the initiative name, ticket name, user details, and action details.
     *
     * @param Ticket $ticket The ticket related to the notification.
     * @param User $actionByUser The user who performed the action.
     * @param object $actionData The data related to the action performed.
     * @return array The payload formatted for Microsoft Teams.
     */
    public static function preparePayload(Ticket $ticket, User $actionByUser, object $actionData)
    {
        return [
            "attachments" => [
                [
                    "contentType" => "application/vnd.microsoft.card.adaptive",
                    "content"     => [
                        "type"    => "AdaptiveCard",
                        "version" => "1.5",
                        "body" => [
                            [
                                "type"   => "TextBlock",
                                "size"   => "Medium",
                                "weight" => "Bolder",
                                "text"   => $ticket->initiative->name
                            ],
                            [
                                "type"   => "TextBlock",
                                "size"   => "Medium",
                                "weight" => "Bolder",
                                "text"   => $ticket->composed_name
                            ],
                            [
                                "type"    => "ColumnSet",
                                "columns" => [
                                    [
                                        "type" => "Column",
                                        "items" => [
                                            [
                                                "type"    => "Image",
                                                "style"   => "Person",
                                                "url"     => $actionByUser->profile_photo_url,
                                                "altText" => $actionByUser->name,
                                                "size"    => "Small",
                                                "width"   => "25px",
                                                "height"  => "25px"
                                            ]
                                        ],
                                        "width" => "auto"
                                    ],
                                    [
                                        "type"  => "Column",
                                        "items" => [
                                            [
                                                "type"   => "TextBlock",
                                                "weight" => "Bolder",
                                                "text"   => self::getActionTitle(actionByUser: $actionByUser, actionData: $actionData),
                                                "wrap"   => true
                                            ]
                                        ],
                                        "width" => "stretch"
                                    ]
                                ]
                            ],
                            [
                                "type" => "TextBlock",
                                "text" => strip_tags($actionData->text),
                                "wrap" => true
                            ]
                        ],
                        "actions" => [
                            [
                                "type"  => "Action.OpenUrl",
                                "title" => "View Ticket",
                                "url"   => url('solution-design/'.$ticket->initiative_id.'/ticket-detail/'.$ticket->id),
                            ]
                        ],
                    ]
                ],
            ]
            ];
    }

    /**
     * Given a user and an action data object, returns a string representing the title of the action
     * 
     * @param User $actionByUser The user who performed the action
     * @param object $actionData The data object representing the action
     * @return string The title of the action
     */
    public static function getActionTitle(User $actionByUser, object $actionData)
    {
        $actionTitle = '';
        switch ($actionData->actionElement) {
            case 'comment':
                if ($actionData->actionType == 'add') {
                    $actionTitle = $actionByUser->name." added a comment";
                } elseif ($actionData->actionType == 'edit') {
                    $actionTitle = $actionByUser->name." edited a comment";
                } else {
                    $actionTitle = $actionByUser->name." deleted a comment";
                }
                break;

            case 'ticket':
                if ($actionData->actionType == 'add') {
                    $actionTitle = $actionByUser->name." created a new ticket";
                } elseif ($actionData->actionType == 'edit') {
                    $actionTitle = $actionByUser->name." edited a ticket";
                } else {
                    $actionTitle = $actionByUser->name." deleted a ticket";
                }
                break;
            
            default:
                # code...
                break;
        }

        return $actionTitle;
    }
}