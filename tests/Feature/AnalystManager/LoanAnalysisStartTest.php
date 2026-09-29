<?php

namespace Tests\Feature\AnalystManager;

use App\Models\Activity;
use App\Models\Loan;
use App\Models\LoanProcess;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanAnalysisStartTest extends TestCase
{
    use DatabaseTransactions;

    public function test_analyst_manager_can_start_loan_analysis(): void
    {
        $member = $this->createUser(
            'member',
            'Member Start Analysis Test'
        );

        $analyst = $this->createUser(
            'analyst_manager',
            'Analyst Start Analysis Test'
        );

        $loan = $this->createLoan($member);

        $this->actingAs($analyst);

        $response = $this->post(
            route(
                'analyst-manager.loans.analysis.start',
                $loan
            )
        );

        $response->assertRedirect(
            route(
                'analyst-manager.loans.show',
                $loan
            )
        );

        $loan->refresh();

        $this->assertSame(
            'under_analysis',
            $loan->status
        );

        $this->assertDatabaseHas(
            'loan_processes',
            [
                'loan_id' => $loan->id,
                'user_id' => $analyst->id,
                'role' => 'analyst_manager',
                'action' => 'analysis_started',
            ]
        );

        $this->assertDatabaseHas(
            'activities',
            [
                'user_id' => $analyst->id,
                'action' => 'loan_analysis_started',
                'subject_id' => $loan->id,
            ]
        );
    }

    public function test_analysis_cannot_be_started_twice(): void
    {
        $member = $this->createUser(
            'member',
            'Member Duplicate Start Test'
        );

        $analyst = $this->createUser(
            'analyst_manager',
            'Analyst Duplicate Start Test'
        );

        $loan = $this->createLoan($member);

        $this->actingAs($analyst);

        $firstResponse = $this->post(
            route(
                'analyst-manager.loans.analysis.start',
                $loan
            )
        );

        $firstResponse->assertRedirect();

        $loan->refresh();

        $this->assertSame(
            'under_analysis',
            $loan->status
        );

        $secondResponse = $this->post(
            route(
                'analyst-manager.loans.analysis.start',
                $loan
            )
        );

        $secondResponse->assertRedirect();

        $loan->refresh();

        $this->assertSame(
            'under_analysis',
            $loan->status
        );

        $this->assertSame(
            1,
            LoanProcess::query()
                ->where('loan_id', $loan->id)
                ->where('action', 'analysis_started')
                ->count()
        );

        $this->assertSame(
            1,
            Activity::query()
                ->where('subject_id', $loan->id)
                ->where('action', 'loan_analysis_started')
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
            'code' => 'KOPKARMADA/TEST-START-ANALYSIS/' .
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
            'purpose_description' => 'Pengujian mulai analisis.',

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
