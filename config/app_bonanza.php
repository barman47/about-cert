<?php

return [
    "signature" => [
        "active" => env("BONANZA_SIGNATURE_ACTIVE", false),
        "type" => env("BONANZA_SIGNATURE_TYPE", "count"), //count | time,
        "count" => env("BONANZA_SIGNATURE_COUNT", 1),
        "time_duration" => env("BONANZA_SIGNATURE_TIME_DURATION", 0),
        "time_unit" => env("BONANZA_SIGNATURE_TIME_DURATION_UNIT", "months"),
        "start_date" => env("BONANZA_SIGNATURE_START_DATE"), // yyyy-mm-dd
        "end_date" => env("BONANZA_SIGNATURE_START_DATE"), // yyyy-mm-dd
    ],
];