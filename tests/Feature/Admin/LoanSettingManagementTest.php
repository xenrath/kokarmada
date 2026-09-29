<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanSettingManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_view_loan_settings(): void
    {
        $admin = $this->createUser('admin');

        $this->actingAs($admin);

        $response = $this->get(
            route('admin.loan-settings.edit')
        );

        $response->assertOk();
        $response->assertSee('Pengaturan Pinjaman');
        $response->assertSee('Minimum Top Up');
        $response->assertSee('Maksimum Total Pinjaman');
        $response->assertSee('Ambang Kapasitas Keuangan');
    }

    public function test_non_admin_cannot_access_loan_settings(): void
    {
        $member = $this->createUser('member');

        $this->actingAs($member);

        $response = $this->get(
            route('admin.loan-settings.edit')
        );

        $response->assertRedirect('/');
    }

    public function test_admin_can_save_loan_settings(): void
    {
        $admin = $this->createUser('admin');

        $this->actingAs($admin);

        $response = $this->put(
            route('admin.loan-settings.update'),
            [
                'top_up_minimum_amount' => 500000,
                'loan_maximum_amount' => 25000000,
                'loan_capacity_threshold_percent' => 40,
            ]
        );

        $response->assertRedirect(
            route('admin.loan-settings.edit')
        );

        $this->assertDatabaseHas('settings', [
            'key' => 'top_up_minimum_amount',
            'value' => '500000',
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'loan_maximum_amount',
            'value' => '25000000',
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'loan_capacity_threshold_percent',
            'value' => '40',
        ]);
    }

    public function test_admin_cannot_save_when_maximum_is_below_minimum_top_up(): void
    {
        $admin = $this->createUser('admin');

        $this->actingAs($admin);

        $response = $this->from(
            route('admin.loan-settings.edit')
        )->put(
            route('admin.loan-settings.update'),
            [
                'top_up_minimum_amount' => 5000000,
                'loan_maximum_amount' => 3000000,
                'loan_capacity_threshold_percent' => 40,
            ]
        );

        $response->assertRedirect(
            route('admin.loan-settings.edit')
        );

        $response->assertSessionHasErrors('loan_maximum_amount');
    }

    public function test_admin_cannot_save_capacity_threshold_above_100_percent(): void
    {
        $admin = $this->createUser('admin');

        $this->actingAs($admin);

        $response = $this->from(
            route('admin.loan-settings.edit')
        )->put(
            route('admin.loan-settings.update'),
            [
                'top_up_minimum_amount' => 500000,
                'loan_maximum_amount' => 25000000,
                'loan_capacity_threshold_percent' => 101,
            ]
        );

        $response->assertRedirect(
            route('admin.loan-settings.edit')
        );

        $response->assertSessionHasErrors(
            'loan_capacity_threshold_percent'
        );
    }

    private function createUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role) . ' Loan Setting Test',
            'nickname' => 'ls_' . substr(uniqid(), 0, 12),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'active',
        ]);
    }
}
