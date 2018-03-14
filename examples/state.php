#!/usr/bin/env php
<?php

require dirname(__DIR__) . "/vendor/autoload.php";

use Amp\Http\Server\CallableResponder;
use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Status;

// Run this script, then visit http://localhost:1337/ in your browser.

Amp\Loop::run(function () {
    $server = new Amp\Http\Server\Server(new CallableResponder(function (Request $request) {
        static $counter = 0;

        // We can keep state between requests, but if you're using multiple server processes,
        // such state will be separate per process.
        // Note: You might see the counter increase by more than one per reload, because browser
        // might try to load a favicon.ico or similar.
        return new Response(Status::OK, [
            "content-type" => "text/plain; charset=utf-8"
        ], "You're visitor #" . (++$counter) . ".");
    }));

    $server->expose("*", 1337);

    yield $server->start();

    // Stop the server when SIGINT is received (this is technically optional, but it is best to call Server::stop()).
    Amp\Loop::onSignal(SIGINT, function (string $watcherId) use ($server) {
        Amp\Loop::cancel($watcherId);
        yield $server->stop();
    });
});