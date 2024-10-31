<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Recipient;
use Illuminate\Http\JsonResponse;

class ClearController extends Controller
{
    public function store(): JsonResponse
    {
        Message::truncate();
        Recipient::truncate();

        return response()->json();
    }
}
