<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegacyFinancialRoutesDisabledTest extends TestCase
{
    public function test_legacy_financial_routes_are_disabled(): void
    {
        $legacyRoutes = [
            '/anggota/simpanan',
            '/anggota/pinjaman',

            '/anggota/ketua/simpanan',
            '/anggota/ketua/pinjaman',

            '/anggota/sekretaris/simpanan',
            '/anggota/sekretaris/pinjaman',

            '/anggota/bendahara/simpanan',
            '/anggota/bendahara/pinjaman',
            '/anggota/bendahara/keuangan-rekening',
            '/anggota/bendahara/keuangan-arus',
            '/anggota/bendahara/keuangan-arus/pemasukan/create',

            '/anggota/manajer/simpanan',
            '/anggota/manajer/pinjaman',

            '/anggota/petugas/simpanan',
            '/anggota/petugas/pinjaman',
        ];

        foreach ($legacyRoutes as $route) {
            $response = $this->get($route);

            $response->assertNotFound();
        }
    }
}
