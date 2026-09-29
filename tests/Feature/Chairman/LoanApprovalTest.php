<?php

namespace Tests\Feature\Chairman;

use App\Models\Loan;
use App\Models\LoanProcess;
use App\Models\LoanTreasurerReview;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanApprovalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_chairman_can_approve_loan_with_final_approved_amount(): void
    {
        $member = $this->createUser(
            'member',
            'Member Chairman Approval Test'
        );

        $treasurer = $this->createUser(
            'treasurer',
            'Treasurer Chairman Approval Test'
        );

        $chairman = $this->createUser(
            'chairman',
            'Chairman Approval Test'
        );

        $loan = $this->createLoan($member);

        LoanTreasurerReview::create([
            'loan_id' => $loan->id,
            'treasurer_id' => $treasurer->id,
            'recommended_amount' => 1500000,
            'notes' => 'Rekomendasi Treasurer.',
            'reviewed_at' => now(),
        ]);

        $this->actingAs($chairman);

        $response = $this->post(
            route(
                'chairman.loans.approve',
                $loan
            ),
            [
                'approved_amount' => 1200000,
                'notes' => 'Persetujuan final Chairman.',
            ]
        );

        $response->assertRedirect();

        $loan->refresh();

        $this->assertSame(
            'approved',
            $loan->status
        );

        $this->assertSame(
            1200000.00,
            (float) $loan->approved_amount
        );

        /*
         * Monthly installment dihitung berdasarkan nominal final Chairman.
         *
         * Rp1.200.000 + bunga 8% = Rp1.296.000
         * Rp1.296.000 / 12 = Rp108.000
         */
        $this->assertSame(
            108000.00,
            (float) $loan->monthly_installment
        );

        /*
         * Rekomendasi Treasurer tetap tersimpan sebagai histori.
         */
        $this->assertDatabaseHas(
            'loan_treasurer_reviews',
            [
                'loan_id' => $loan->id,
                'treasurer_id' => $treasurer->id,
                'recommended_amount' => 1500000,
            ]
        );

        $this->assertDatabaseHas(
            'loan_processes',
            [
                'loan_id' => $loan->id,
                'user_id' => $chairman->id,
                'role' => 'chairman',
                'action' => 'loan_approved',
            ]
        );

        $this->assertDatabaseHas(
            'activities',
            [
                'user_id' => $chairman->id,
                'action' => 'loan_approved',
                'subject_id' => $loan->id,
            ]
        );
    }

    public function test_approved_loan_cannot_be_approved_again(): void
    {
        $member = $this->createUser(
            'member',
            'Member Chairman Duplicate Test'
        );

        $chairman = $this->createUser(
            'chairman',
            'Chairman Duplicate Test'
        );

        $loan = $this->createLoan($member);

        $this->actingAs($chairman);

        $firstResponse = $this->post(
            route(
                'chairman.loans.approve',
                $loan
            ),
            [
                'approved_amount' => 1200000,
                'notes' => 'Persetujuan pertama.',
            ]
        );

        $firstResponse->assertRedirect();

        $loan->refresh();

        $this->assertSame(
            'approved',
            $loan->status
        );

        $this->assertSame(
            1200000.00,
            (float) $loan->approved_amount
        );

        $secondResponse = $this->post(
            route(
                'chairman.loans.approve',
                $loan
            ),
            [
                'approved_amount' => 1000000,
                'notes' => 'Percobaan persetujuan kedua.',
            ]
        );

        $secondResponse->assertRedirect();

        $loan->refresh();

        $this->assertSame(
            'approved',
            $loan->status
        );

        $this->assertSame(
            1200000.00,
            (float) $loan->approved_amount
        );

        $this->assertSame(
            1,
            LoanProcess::query()
                ->where('loan_id', $loan->id)
                ->where('action', 'loan_approved')
                ->count()
        );
    }

    private function createUser(
        string $role,
        string $name
    ): User {
        return User::create([
            'name' => $name,
            'nickname' => 'test_' . uniqid(),
            'phone' => '08' . random_int(
                1000000000,
                9999999999
            ),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'active',
        ]);
    }

    private function createLoan(
        User $member
    ): Loan {
        $sequenceNumber = ((int) Loan::max(
            'sequence_number'
        )) + 1;

        return Loan::create([
            'code' => 'KOPKARMADA/TEST-CHAIRMAN/' .
                now()->format('YmdHis') .
                '/' .
                $sequenceNumber,

            'sequence_number' => $sequenceNumber,
            'user_id' => $member->id,
            'submitted_at' => now(),

            'requested_amount' => 1800000,

            'purpose_category' => 'consumer',
            'business_type' => null,
            'business_type_other' => null,
            'purpose_description' => 'Pengujian approval Chairman.',

            'term_months' => 12,
            'repayment_type' => 'monthly',
            'monthly_installment' => null,

            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 1,

            'other_monthly_income' => 0,

            'salary_slip' => 'private/test/salary-slip/test.pdf',

            'interest_rate' => 8,
            'approved_amount' => null,

            'status' => 'waiting_chairman_approval',
        ]);
    }
}
