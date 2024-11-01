# laravel-statsd

## Installation

Install the latest version with

```bash
composer require florinmotoc/laravel-statsd
```

## Basic Usage

```dotenv
FM_STATSD_CLIENT=FM_STATSD_CLIENT_CUSTOM_DATADOG
FM_LARAVEL_STATSD_JOB_TIME_ENABLED=true
```

- set `FM_LARAVEL_STATSD_JOB_TIME_ENABLED=true` in your `.env` file if you want to send laravel queue job times to statsd
