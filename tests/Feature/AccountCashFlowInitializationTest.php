<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\CashFlow;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AccountCashFlowInitializationTest extends TestCase
{
    use DatabaseTransactions;

    private function createLegacyAccount(): Account
    {
        $account = Account::withoutEvents(function () {
            return Account::create([
                'type' => 'bank',
                'name' => 'Legacy CashFlow Test',
                'opening_balance' => 0,
                'is_active' => true,
            ]);
        });

        return $account->refresh();
    }

    public function test_new_account_is_automatically_marked_as_initialized(): void
    {
        $account = Account::create([
            'type' => 'bank',
            'name' => 'New Account Initialization Test',
            'opening_balance' => 1000000,
            'is_active' => true,
        ]);

        $this->assertNotNull(
            $account->opening_balance_initialized_at
        );
    }

    public function test_uninitialized_legacy_account_cannot_receive_cashflow_in(): void
    {
        $account = $this->createLegacyAccount();

        $this->assertNull(
            $account->opening_balance_initialized_at
        );

        $this->expectException(
            ValidationException::class
        );

        CashFlow::create([
            'account_id' => $account->id,
            'loan_id' => null,
            'installment_id' => null,
            'type' => 'in',
            'category' => 'other_income',
            'amount' => 250000,
            'description' => 'Test legacy inflow',
            'occurred_at' => now(),
        ]);
    }

    public function test_uninitialized_legacy_account_cannot_receive_cashflow_out(): void
    {
        $account = $this->createLegacyAccount();

        $this->assertNull(
            $account->opening_balance_initialized_at
        );

        DB::transaction(function () use ($account) {
            $this->expectException(
                ValidationException::class
            );

            CashFlow::create([
                'account_id' => $account->id,
                'loan_id' => null,
                'installment_id' => null,
                'type' => 'out',
                'category' => 'other_expense',
                'amount' => 250000,
                'description' => 'Test legacy outflow',
                'occurred_at' => now(),
            ]);
        });
    }

    public function test_initialized_account_can_receive_cashflow_in(): void
    {
        $account = Account::create([
            'type' => 'bank',
            'name' => 'Initialized CashFlow Test',
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
            'description' => 'Test initialized inflow',
            'occurred_at' => now(),
        ]);

        $account->refresh();

        $this->assertSame(
            1250000.0,
            $account->calculateBalance()
        );

        $this->assertDatabaseHas('cash_flows', [
            'account_id' => $account->id,
            'type' => 'in',
            'amount' => 250000,
        ]);
    }
}
