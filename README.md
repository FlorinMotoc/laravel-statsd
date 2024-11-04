# laravel-statsd

## Installation

Install the latest version with

```bash
composer require florinmotoc/laravel-statsd
```

## Basic Usage

```dotenv
FM_LARAVEL_STATSD_CLIENT=FM_LARAVEL_STATSD_CLIENT_CUSTOM_DATADOG
FM_LARAVEL_STATSD_JOB_TIME_ENABLED=true
```

- set `FM_LARAVEL_STATSD_JOB_TIME_ENABLED=true` in your `.env` file if you want to send laravel queue job times to statsd
- set `FM_LARAVEL_STATSD_CLIENT` to any of:
  - check `florinmotoc/php-statsd-client` for more .env vars and infos: https://github.com/FlorinMotoc/php-statsd-client
