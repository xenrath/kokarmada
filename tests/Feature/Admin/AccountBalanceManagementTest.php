<?php

namespace Tests\Feature\Admin;

use App\Models\Account;
use App\Models\CashFlow;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountBalanceManagementTest extends TestCase
{
    use DatabaseTransactions;

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin Account Test',
            'nickname' => 'admin_account_' . uniqid(),
            'phone' => '08' . mt_rand(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_create_account_with_opening_balance(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin);

        $response = $this->post(
            route('admin.accounts.store'),
            [
                'type' => 'bank',
                'name' => 'Rekening Operasional Test',
                'bank_name' => 'Bank Test',
                'account_number' => 'TEST-' . uniqid(),
                'account_name' => 'KOPKARMADA TEST',
                'opening_balance' => 2500000,
            ]
        );

        $response->assertRedirect(
            route('admin.accounts.index')
        );

        $this->assertDatabaseHas('accounts', [
            'name' => 'Rekening Operasional Test',
            'opening_balance' => 2500000,
        ]);
    }

    public function test_admin_can_change_opening_balance_before_first_cashflow(): void
    {
        $admin = $this->createAdmin();

        $account = Account::create([
            'type' => 'bank',
            'name' => 'Account Edit Balance Test',
            'opening_balance' => 1000000,
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->put(
            route('admin.accounts.update', $account),
            [
                'type' => $account->type,
                'name' => $account->name,
                'bank_name' => $account->bank_name,
                'account_number' => $account->account_number,
                'account_name' => $account->account_name,
                'opening_balance' => 1500000,
            ]
        );

        $response->assertRedirect(
            route('admin.accounts.index')
        );

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'opening_balance' => 1500000,
        ]);
    }

    public function test_admin_cannot_change_opening_balance_after_account_has_cashflow(): void
    {
        $admin = $this->createAdmin();

        $account = Account::create([
            'type' => 'bank',
            'name' => 'Locked Opening Balance Test',
            'opening_balance' => 1000000,
            'is_active' => true,
        ]);

        CashFlow::create([
            'account_id' => $account->id,
            'loan_id' => null,
            'installment_id' => null,
            'type' => 'in',
            'category' => 'other_income',
            'amount' => 250000,
            'description' => 'Test transaction',
            'occurred_at' => now(),
        ]);

        $this->actingAs($admin);

        $response = $this->from(
            route('admin.accounts.edit', $account)
        )->put(
            route('admin.accounts.update', $account),
            [
                'type' => $account->type,
                'name' => $account->name,
                'bank_name' => $account->bank_name,
                'account_number' => $account->account_number,
                'account_name' => $account->account_name,
                'opening_balance' => 5000000,
            ]
        );

        $response->assertRedirect(
            route('admin.accounts.edit', $account)
        );

        $response->assertSessionHasErrors('opening_balance');

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'opening_balance' => 1000000,
        ]);
    }

    public function test_admin_can_update_other_account_data_after_opening_balance_is_locked(): void
    {
        $admin = $this->createAdmin();

        $account = Account::create([
            'type' => 'bank',
            'name' => 'Account Data Test',
            'bank_name' => 'Bank Lama',
            'opening_balance' => 1000000,
            'is_active' => true,
        ]);

        CashFlow::create([
            'account_id' => $account->id,
            'loan_id' => null,
            'installment_id' => null,
            'type' => 'in',
            'category' => 'other_income',
            'amount' => 500000,
            'description' => 'Test transaction',
            'occurred_at' => now(),
        ]);

        $this->actingAs($admin);

        $response = $this->put(
            route('admin.accounts.update', $account),
            [
                'type' => 'bank',
                'name' => 'Account Data Test Updated',
                'bank_name' => 'Bank Baru',
                'account_number' => '123456',
                'account_name' => 'KOPKARMADA',
            ]
        );

        $response->assertRedirect(
            route('admin.accounts.index')
        );

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'name' => 'Account Data Test Updated',
            'bank_name' => 'Bank Baru',
            'account_number' => '123456',
            'account_name' => 'KOPKARMADA',
            'opening_balance' => 1000000,
        ]);
    }
}
