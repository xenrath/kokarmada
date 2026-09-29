<?php

namespace Tests\Feature\AnalystManager;

use App\Models\Loan;
use App\Models\LoanAnalysis;
use App\Models\LoanProcess;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanAnalysisTest extends TestCase
{
    use DatabaseTransactions;

    public function test_analyst_manager_can_complete_loan_analysis(): void
    {
        $member = $this->createUser(
            'member',
            'Member Analysis Test'
        );

        $analyst = $this->createUser(
            'analyst_manager',
            'Analyst Analysis Test'
        );

        $loan = $this->createLoan($member);

        $this->actingAs($analyst);

        $response = $this->post(
            route(
                'analyst-manager.loans.analysis',
                $loan
            ),
            [
                'recommended_amount' => 1600000,
                'notes' => 'Hasil analisis kemampuan pembayaran anggota.',
            ]
        );

        $response->assertRedirect();

        $loan->refresh();

        $this->assertSame(
            'waiting_treasurer_review',
            $loan->status
        );

        $this->assertDatabaseHas('loan_analyses', [
            'loan_id' => $loan->id,
            'analyst_id' => $analyst->id,
            'recommended_amount' => 1600000,
            'notes' => 'Hasil analisis kemampuan pembayaran anggota.',
        ]);

        $this->assertDatabaseHas('loan_processes', [
            'loan_id' => $loan->id,
            'user_id' => $analyst->id,
            'role' => 'analyst_manager',
            'action' => 'analysis_completed',
            'notes' => 'Hasil analisis kemampuan pembayaran anggota.',
        ]);
    }

    public function test_completed_analysis_cannot_be_submitted_again(): void
    {
        $member = $this->createUser(
            'member',
            'Member Analysis Duplicate Test'
        );

        $analyst = $this->createUser(
            'analyst_manager',
            'Analyst Analysis Duplicate Test'
        );

        $loan = $this->createLoan($member);

        $this->actingAs($analyst);

        $firstResponse = $this->post(
            route(
                'analyst-manager.loans.analysis',
                $loan
            ),
            [
                'recommended_amount' => 1600000,
                'notes' => 'Analisis pertama.',
            ]
        );

        $firstResponse->assertRedirect();

        $loan->refresh();

        $this->assertSame(
            'waiting_treasurer_review',
            $loan->status
        );

        $analysis = LoanAnalysis::query()
            ->where('loan_id', $loan->id)
            ->firstOrFail();

        $secondResponse = $this->post(
            route(
                'analyst-manager.loans.analysis',
                $loan
            ),
            [
                'recommended_amount' => 1200000,
                'notes' => 'Percobaan analisis kedua.',
            ]
        );

        $secondResponse->assertRedirect();

        $analysis->refresh();
        $loan->refresh();

        $this->assertSame(
            'waiting_treasurer_review',
            $loan->status
        );

        $this->assertSame(
            1600000.00,
            (float) $analysis->recommended_amount
        );

        $this->assertSame(
            'Analisis pertama.',
            $analysis->notes
        );

        $this->assertSame(
            1,
            LoanProcess::query()
                ->where('loan_id', $loan->id)
                ->where('action', 'analysis_completed')
                ->count()
        );
    }

    private function createUser(
        string $role,
        string $name
    ): User {
        return User::create([
            'name' => $name,
            'nickname' => strtolower(
                str_replace(
                    ' ',
                    '_',
                    $name
                )
            ) . '_' . uniqid(),

            'phone' => '08' . random_int(
                1000000000,
                9999999999
            ),

            'gender' => 'L',

            'password' => Hash::make(
                'password'
            ),

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
            'code' => 'KOPKARMADA/TEST-ANALYSIS/' .
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
            'purpose_description' => 'Pengujian analisis pinjaman.',

            'term_months' => 12,
            'repayment_type' => 'monthly',
            'monthly_installment' => 162000,

            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 1,

            'other_monthly_income' => 0,

            'salary_slip' => 'private/test/salary-slip/test.pdf',

            'interest_rate' => 8,
            'approved_amount' => null,

            'status' => 'submitted',
        ]);
    }
}
