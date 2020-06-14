<?php

namespace Spatie\Snapshots\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;
use RuntimeException;
use Spatie\Snapshots\MatchesSnapshots;

class SnapshotsServiceProvider extends ServiceProvider
{
    public function register()
    {
        TestResponse::macro('assertMatchesHtmlSnapshot', function () {
            $backtrace = collect(debug_backtrace());
            $index = $backtrace->search(function (array $call): bool {
                return ($call['class'] ?? null) === TestResponse::class
                    && ($call['function'] ?? null) === '__call'
                    && ($call['args'][0] ?? null) === 'assertMatchesHtmlSnapshot';
            });
            $test = $backtrace->get($index+1);

            if(!isset(class_uses($test['object'])[MatchesSnapshots::class])) {
                throw new RuntimeException(sprintf('%s does not use %s', $test['class'], MatchesSnapshots::class));
            }

            /** @var TestResponse $response */
            $response = $this;
            call_user_func([$test['object'], 'assertMatchesHtmlSnapshot'], $response->getContent());
        });
    }
}
