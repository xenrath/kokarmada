<?php

namespace Tests\Feature\Treasurer;

use App\Models\Loan;
use App\Models\LoanProcess;
use App\Models\LoanTreasurerReview;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanReviewTest extends TestCase
{
    use DatabaseTransactions;

    public function test_treasurer_can_complete_loan_review(): void
    {
        $member = $this->createUser(
            'member',
            'Member Treasurer Review Test'
        );

        $treasurer = $this->createUser(
            'treasurer',
            'Treasurer Review Test'
        );

        $loan = $this->createLoan($member);

        $this->actingAs($treasurer);

        $response = $this->post(
            route(
                'treasurer.loans.review',
                $loan
            ),
            [
                'recommended_amount' => 1500000,
                'notes' => 'Hasil review Treasurer atas kemampuan dan kredibilitas anggota.',
            ]
        );

        $response->assertRedirect();

        $loan->refresh();

        $this->assertSame(
            'waiting_chairman_approval',
            $loan->status
        );

        $this->assertDatabaseHas(
            'loan_treasurer_reviews',
            [
                'loan_id' => $loan->id,
                'treasurer_id' => $treasurer->id,
                'recommended_amount' => 1500000,
                'notes' => 'Hasil review Treasurer atas kemampuan dan kredibilitas anggota.',
            ]
        );

        $this->assertDatabaseHas(
            'loan_processes',
            [
                'loan_id' => $loan->id,
                'user_id' => $treasurer->id,
                'role' => 'treasurer',
                'action' => 'review_completed',
                'notes' => 'Hasil review Treasurer atas kemampuan dan kredibilitas anggota.',
            ]
        );

        $this->assertNull(
            $loan->approved_amount
        );
    }

    public function test_completed_treasurer_review_cannot_be_submitted_again(): void
    {
        $member = $this->createUser(
            'member',
            'Member Treasurer Review Duplicate Test'
        );

        $treasurer = $this->createUser(
            'treasurer',
            'Treasurer Review Duplicate Test'
        );

        $loan = $this->createLoan($member);

        $this->actingAs($treasurer);

        $firstResponse = $this->post(
            route(
                'treasurer.loans.review',
                $loan
            ),
            [
                'recommended_amount' => 1500000,
                'notes' => 'Review Treasurer pertama.',
            ]
        );

        $firstResponse->assertRedirect();

        $loan->refresh();

        $this->assertSame(
            'waiting_chairman_approval',
            $loan->status
        );

        $review = LoanTreasurerReview::query()
            ->where('loan_id', $loan->id)
            ->firstOrFail();

        $secondResponse = $this->post(
            route(
                'treasurer.loans.review',
                $loan
            ),
            [
                'recommended_amount' => 1200000,
                'notes' => 'Percobaan review kedua.',
            ]
        );

        $secondResponse->assertRedirect();

        $review->refresh();
        $loan->refresh();

        $this->assertSame(
            'waiting_chairman_approval',
            $loan->status
        );

        $this->assertSame(
            1500000.00,
            (float) $review->recommended_amount
        );

        $this->assertSame(
            'Review Treasurer pertama.',
            $review->notes
        );

        $this->assertSame(
            1,
            LoanProcess::query()
                ->where('loan_id', $loan->id)
                ->where('action', 'review_completed')
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
            'code' => 'KOPKARMADA/TEST-TREASURER/' .
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
            'purpose_description' => 'Pengujian review Treasurer.',

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

            'status' => 'waiting_treasurer_review',
        ]);
    }
}
