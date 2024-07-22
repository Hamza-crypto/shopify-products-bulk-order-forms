<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class WebhookController extends Controller
{
    public $asana_controller;

    public function __construct()
    {

    }

    public function webhook(Request $request)
    {
        $this->asana_controller = new AsanaController();

        $nameFromApi = "Random" . time(); // Replace with actual API call if needed

        $tasks = [
            $nameFromApi . "- Email storage provider",
            $nameFromApi . "- Create storage booking in CRM",
            $nameFromApi . "- Email storage agreement"
        ];

        foreach ($tasks as $taskName) {
            $body = [
                'data' => [
                    'name' => $taskName,
                    'workspace' => env('ASANA_WORKSPACE'),
                    'assignee_section' => env('ASANA_SECTION'),
                    'projects' => [
                        env('ASANA_PROJECT_ID')
                    ]
                ]
            ];

            $res = $this->asana_controller->call('tasks', 'POST', $body);
            dump($res);
        }
    }

    public function create_webhook(Request $request)
    {
         // Handle the handshake confirmation
        if ($request->hasHeader('X-Hook-Secret')) {
            return response('', 200)
                ->header('X-Hook-Secret', $request->header('X-Hook-Secret'));
        }
    }
}