<?php

return [
    "driver" => getenv("ICICLE_DRIVER"),
    "schema" => getenv("ICICLE_SCHEMA"),
    "username" => getenv("ICICLE_USERNAME"),
    "password" => getenv("ICICLE_PASSWORD"),
    "log" => __DIR__,
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
