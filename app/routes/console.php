<?php

use App\Models\Recipient;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('tmp', function () {
    $items = [
        ['postfix_id' => 'F027D707C81', 'status' => 'tmp11', 'response' => 'tmp22', 'message_id' => '123', 'batch_id' => '124', 'email' => 'email_here', 'replacements' => json_encode([])]
    ];

    $values = collect($items)->map(function ($row) {
        return "('" . $row['postfix_id'] . "', '" . $row['message_id'] . "', '" . $row['batch_id'] . "', '" . $row['email'] . "', '" . $row['replacements'] . "', '" . $row['status'] . "', '" . $row['response'] . "')";
    })->implode(',');

    Recipient::upsert($items, ['postfix_id'], ['status', 'response']);
    die;

    $sql = "
    INSERT INTO recipients (postfix_id, message_id, batch_id, email, replacements, status, response)
    VALUES $values AS new_data
    ON DUPLICATE KEY UPDATE
        recipients.status = new_data.status,
        recipients.response = new_data.response;
";

    DB::statement($sql);
});
