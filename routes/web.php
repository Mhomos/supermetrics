<?php

use App\Controllers\HomeController;
use App\Controllers\MetricReportController;

$app->map('/', [new HomeController(), 'index']);
$app->map('/metric/{report_slug}', [new MetricReportController(), 'show']);
