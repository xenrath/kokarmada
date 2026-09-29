<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LegacyFileRoutesDisabledTest extends TestCase
{
    public function test_legacy_file_routes_are_disabled(): void
    {
        $this->assertFalse(
            Route::has('files.user-detail')
        );

        $this->assertFalse(
            Route::has('files.pinjaman')
        );
    }
}
