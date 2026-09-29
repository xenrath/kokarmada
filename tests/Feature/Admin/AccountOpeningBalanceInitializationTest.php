<?php

namespace Tests\Feature\Admin;

use App\Models\Account;
use App\Models\CashFlow;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountOpeningBalanceInitializationTest extends TestCase
{
    use DatabaseTransactions;

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin Initialization Test',
            'nickname' => 'admin_initialization_' . uniqid(),
            'phone' => '08' . mt_rand(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    private function createLegacyAccount(): Account
    {
        $account = Account::withoutEvents(function () {
            return Account::create([
                'type' => 'bank',
                'name' => 'Legacy Account Test',
                'opening_balance' => 0,
                'is_active' => true,
            ]);
        });

        DB::table('cash_flows')->insert([
            'account_id' => $account->id,
            'loan_id' => null,
            'installment_id' => null,
            'type' => 'out',
            'category' => 'other_expense',
            'amount' => 250000,
            'description' => 'Legacy transaction',
            'occurred_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $account->refresh();
    }

    public function test_admin_can_initialize_opening_balance_for_legacy_account(): void
    {
        $admin = $this->createAdmin();
        $account = $this->createLegacyAccount();

        $this->assertTrue(
            $account->needsOpeningBalanceInitialization()
        );

        $this->actingAs($admin);

        $response = $this->patch(
            route(
                'admin.accounts.initialize-opening-balance',
                $account
            ),
            [
                'opening_balance' => 1000000,
            ]
        );

        $response->assertRedirect(
            route('admin.accounts.index')
        );

        $account->refresh();

        $this->assertSame(
            1000000.0,
            (float) $account->opening_balance
        );

        $this->assertNotNull(
            $account->opening_balance_initialized_at
        );

        $this->assertFalse(
            $account->needsOpeningBalanceInitialization()
        );

        $this->assertSame(
            750000.0,
            $account->calculateBalance()
        );
    }

    public function test_opening_balance_cannot_be_initialized_twice(): void
    {
        $admin = $this->createAdmin();
        $account = $this->createLegacyAccount();

        $account->update([
            'opening_balance' => 1000000,
            'opening_balance_initialized_at' => now(),
        ]);

        $this->actingAs($admin);

        $response = $this->patch(
            route(
                'admin.accounts.initialize-opening-balance',
                $account
            ),
            [
                'opening_balance' => 5000000,
            ]
        );

        $response->assertStatus(422);

        $account->refresh();

        $this->assertSame(
            1000000.0,
            (float) $account->opening_balance
        );
    }

    public function test_account_without_cashflow_cannot_use_legacy_initialization_endpoint(): void
    {
        $admin = $this->createAdmin();

        $account = Account::create([
            'type' => 'bank',
            'name' => 'New Account Test',
            'opening_balance' => 500000,
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->patch(
            route(
                'admin.accounts.initialize-opening-balance',
                $account
            ),
            [
                'opening_balance' => 1000000,
            ]
        );

        $response->assertStatus(422);

        $account->refresh();

        $this->assertSame(
            500000.0,
            (float) $account->opening_balance
        );
    }
}
