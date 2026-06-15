<?php

declare(strict_types=1);

use Tests\TestCase;

require_once __DIR__.'/TestCase.php';

uses(TestCase::class)->in(__DIR__.'/Feature', __DIR__.'/Unit');
