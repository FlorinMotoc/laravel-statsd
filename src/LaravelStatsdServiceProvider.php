<?php

namespace FlorinMotoc\LaravelStatsd;

use FlorinMotoc\Statsd\ArrayStatsdClient;
use FlorinMotoc\Statsd\CustomDatadogStatsdClient;
use FlorinMotoc\Statsd\DatadogStatsdClient;
use FlorinMotoc\Statsd\NullStatsdClient;
use FlorinMotoc\Statsd\StatsdClientInterface;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class LaravelStatsdServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (env('FM_LARAVEL_STATSD_CLIENT') == 'FM_LARAVEL_STATSD_CLIENT_DATADOG') {
            $this->app->singleton(StatsdClientInterface::class, DatadogStatsdClient::class);
        } elseif (env('FM_LARAVEL_STATSD_CLIENT') == 'FM_LARAVEL_STATSD_CLIENT_CUSTOM_DATADOG') {
            $this->app->singleton(StatsdClientInterface::class, CustomDatadogStatsdClient::class);
        } elseif (env('FM_LARAVEL_STATSD_CLIENT') == 'FM_LARAVEL_STATSD_CLIENT_ARRAY') {
            $this->app->singleton(StatsdClientInterface::class, ArrayStatsdClient::class);
        } elseif (env('FM_LARAVEL_STATSD_CLIENT') == 'FM_LARAVEL_STATSD_CLIENT_NULL') {
            $this->app->singleton(StatsdClientInterface::class, NullStatsdClient::class);
        } else {
            $this->app->singleton(StatsdClientInterface::class, NullStatsdClient::class);
        }

        $this->app->singleton(DatadogStatsdClient::class);
        $this->app->singleton(CustomDatadogStatsdClient::class);
        $this->app->singleton(ArrayStatsdClient::class);
        $this->app->singleton(NullStatsdClient::class);
    }

    public function boot()
    {
        $this->sendStatsdQueueJobsProcessingTime();
    }

    private function sendStatsdQueueJobsProcessingTime(): void
    {
        if (!env('FM_LARAVEL_STATSD_JOB_TIME_ENABLED')) {
            return;
        }

        Queue::before(function (JobProcessing $event) {
            try {
                $event->job->_logJobTime_StartAt = microtime(1); // maybe a better idea for the variable transfer?
            } catch (\Throwable $e) {
                Log::error(sprintf("LaravelStatsdServiceProvider logJobTime error @ before: %s @ %s @ %s", $e->getMessage(), $e->getFile(), $e->getLine()));
            }
        });

        Queue::after(function (JobProcessed $event) {
            try {
                // maybe a better idea for the variable transfer? :: $event->job->_logJobTime_StartAt
                /** @var StatsdClientInterface $statsdClient */
                $statsdClient = $this->app->get(StatsdClientInterface::class);
                $statsdClient->microtiming(
                    'dogstatsd.time.queue.job',
                    (microtime(1) - $event->job->_logJobTime_StartAt),
                    ['class' => $event->job->payload()['displayName'] ?? 'null']
                );
            } catch (\Throwable $e) {
                Log::error(sprintf("LaravelStatsdServiceProvider logJobTime error @ after: %s @ %s @ %s", $e->getMessage(), $e->getFile(), $e->getLine()));
            }
        });
    }
}
