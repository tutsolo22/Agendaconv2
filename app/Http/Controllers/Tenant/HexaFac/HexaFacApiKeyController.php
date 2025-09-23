<?php

namespace App\Http\Controllers\Tenant\HexaFac;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HexaFacApiKeyController extends Controller
{
    public function index($applicationId)
    {
        // Logic to list API keys for an application
    }

    public function create($applicationId)
    {
        // Logic to show the create form
    }

    public function store(Request $request, $applicationId)
    {
        // Logic to store a new API key
    }

    public function edit($applicationId, $id)
    {
        // Logic to show the edit form
    }

    public function update(Request $request, $applicationId, $id)
    {
        // Logic to update an API key
    }

    public function destroy($applicationId, $id)
    {
        // Logic to delete an API key
    }
}
