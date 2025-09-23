<?php

namespace App\Http\Controllers\Tenant\HexaFac;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HexaFacWebhookController extends Controller
{
    public function index($applicationId)
    {
        // Logic to list webhooks for an application
    }

    public function create($applicationId)
    {
        // Logic to show the create form
    }

    public function store(Request $request, $applicationId)
    {
        // Logic to store a new webhook
    }

    public function edit($applicationId, $id)
    {
        // Logic to show the edit form
    }

    public function update(Request $request, $applicationId, $id)
    {
        // Logic to update a webhook
    }

    public function destroy($applicationId, $id)
    {
        // Logic to delete a webhook
    }
}
