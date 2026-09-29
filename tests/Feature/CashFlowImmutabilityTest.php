<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\CashFlow;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CashFlowImmutabilityTest extends TestCase
{
    use DatabaseTransactions;

    private function createAccount(): Account
    {
        return Account::create([
            'type' => 'bank',
            'name' => 'CashFlow Immutability Test',
            'opening_balance' => 1000000,
            'is_active' => true,
        ]);
    }

    private function createCashFlow(Account $account): CashFlow
    {
        return CashFlow::create([
            'account_id' => $account->id,
            'loan_id' => null,
            'installment_id' => null,
            'type' => 'in',
            'category' => 'other_income',
            'amount' => 250000,
            'description' => 'Original cash flow',
            'occurred_at' => now(),
        ]);
    }

    public function test_existing_cashflow_cannot_be_updated(): void
    {
        $account = $this->createAccount();
        $cashFlow = $this->createCashFlow($account);

        $originalAmount = $cashFlow->amount;
        $originalDescription = $cashFlow->description;

        $this->expectException(
            ValidationException::class
        );

        $cashFlow->update([
            'amount' => 500000,
            'description' => 'Changed cash flow',
        ]);

        $this->assertSame(
            250000.00,
            (float) CashFlow::findOrFail($cashFlow->id)->amount
        );

        $this->assertSame(
            $originalDescription,
            CashFlow::findOrFail($cashFlow->id)->description
        );

        $this->assertSame(
            $originalAmount,
            $cashFlow->amount
        );
    }

    public function test_existing_cashflow_cannot_be_deleted(): void
    {
        $account = $this->createAccount();
        $cashFlow = $this->createCashFlow($account);

        $this->expectException(
            ValidationException::class
        );

        $cashFlow->delete();

        $this->assertDatabaseHas('cash_flows', [
            'id' => $cashFlow->id,
            'amount' => 250000,
            'description' => 'Original cash flow',
        ]);
    }

    public function test_account_balance_remains_unchanged_when_cashflow_update_is_rejected(): void
    {
        $account = $this->createAccount();
        $cashFlow = $this->createCashFlow($account);

        $this->assertSame(
            1250000.0,
            $account->calculateBalance()
        );

        try {
            $cashFlow->update([
                'amount' => 500000,
            ]);
        } catch (ValidationException) {
            // Expected.
        }

        $account->refresh();

        $this->assertSame(
            1250000.0,
            $account->calculateBalance()
        );
    }

    public function test_account_balance_remains_unchanged_when_cashflow_delete_is_rejected(): void
    {
        $account = $this->createAccount();
        $cashFlow = $this->createCashFlow($account);

        $this->assertSame(
            1250000.0,
            $account->calculateBalance()
        );

        try {
            $cashFlow->delete();
        } catch (ValidationException) {
            // Expected.
        }

        $account->refresh();

        $this->assertSame(
            1250000.0,
            $account->calculateBalance()
        );

        $this->assertDatabaseHas(
            'cash_flows',
            [
                'id' => $cashFlow->id,
            ]
        );
    }
}
