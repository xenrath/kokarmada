<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\CashFlow;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AccountBalanceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_account_balance_is_opening_balance_when_there_are_no_cashflows(): void
    {
        $account = Account::create([
            'type' => 'bank',
            'name' => 'Account Opening Balance Test',
            'opening_balance' => 500000,
            'is_active' => true,
        ]);

        $account->refresh();

        $this->assertSame(
            500000.0,
            (float) $account->opening_balance
        );

        $this->assertSame(
            500000.0,
            $account->calculateBalance()
        );
    }

    public function test_account_balance_is_opening_balance_plus_inflow_less_outflow(): void
    {
        $account = Account::create([
            'type' => 'bank',
            'name' => 'Account Balance Test',
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
            'description' => 'Test inflow',
            'occurred_at' => now(),
        ]);

        CashFlow::create([
            'account_id' => $account->id,
            'loan_id' => null,
            'installment_id' => null,
            'type' => 'out',
            'category' => 'other_expense',
            'amount' => 175000,
            'description' => 'Test outflow',
            'occurred_at' => now(),
        ]);

        $account->refresh();

        $this->assertSame(
            1075000.0,
            $account->calculateBalance()
        );
    }

    public function test_cashflow_out_is_rejected_when_outflow_exceeds_available_balance(): void
    {
        $account = Account::create([
            'type' => 'cash',
            'name' => 'Negative Balance Prevention Test',
            'opening_balance' => 500000,
            'is_active' => true,
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        CashFlow::create([
            'account_id' => $account->id,
            'loan_id' => null,
            'installment_id' => null,
            'type' => 'out',
            'category' => 'other_expense',
            'amount' => 600000,
            'description' => 'Test outflow',
            'occurred_at' => now(),
        ]);
    }

    public function test_cashflow_out_is_allowed_when_amount_equals_available_balance(): void
    {
        $account = Account::create([
            'type' => 'cash',
            'name' => 'Exact Balance Test',
            'opening_balance' => 500000,
            'is_active' => true,
        ]);

        CashFlow::create([
            'account_id' => $account->id,
            'loan_id' => null,
            'installment_id' => null,
            'type' => 'out',
            'category' => 'other_expense',
            'amount' => 500000,
            'description' => 'Test exact balance outflow',
            'occurred_at' => now(),
        ]);

        $account->refresh();

        $this->assertSame(
            0.0,
            $account->calculateBalance()
        );

        $this->assertDatabaseHas('cash_flows', [
            'account_id' => $account->id,
            'type' => 'out',
            'amount' => 500000,
        ]);
    }

    public function test_cashflow_out_is_rejected_for_inactive_account(): void
    {
        $account = Account::create([
            'type' => 'bank',
            'name' => 'Inactive Account Test',
            'opening_balance' => 1000000,
            'is_active' => false,
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        CashFlow::create([
            'account_id' => $account->id,
            'loan_id' => null,
            'installment_id' => null,
            'type' => 'out',
            'category' => 'other_expense',
            'amount' => 250000,
            'description' => 'Test inactive account',
            'occurred_at' => now(),
        ]);
    }
}
