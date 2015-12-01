<?php

return [
    "driver" => getenv("ICICLE_DRIVER"),
    "database" => getenv("ICICLE_DATABASE"),
    "username" => getenv("ICICLE_USERNAME"),
    "password" => getenv("ICICLE_PASSWORD"),
    "remit" => [
        "driver" => "zeromq",
        "server" => [
            "port" => getenv("ICICLE_REMIT_SERVER_PORT"),
        ],
        "client" => [
            "port" => getenv("ICICLE_REMIT_CLIENT_PORT"),
        ],
    ],
];
