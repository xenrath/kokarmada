<?php

namespace Tests\Feature\Member;

use App\Models\Loan;
use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LoanAuthorizationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_member_can_view_own_loan(): void
    {
        $member = $this->createUser(
            'member',
            'Member Own Loan Test'
        );

        $loan = $this->createLoan($member);

        $this->actingAs($member);

        $response = $this->get(
            route(
                'member.loans.show',
                $loan
            )
        );

        $response->assertOk();
        $response->assertViewIs('member.loans.show');
    }

    public function test_member_cannot_view_another_members_loan(): void
    {
        $memberA = $this->createUser(
            'member',
            'Member Authorization A'
        );

        $memberB = $this->createUser(
            'member',
            'Member Authorization B'
        );

        $loan = $this->createLoan($memberA);

        $this->actingAs($memberB);

        $response = $this->get(
            route(
                'member.loans.show',
                $loan
            )
        );

        $response->assertNotFound();
    }

    public function test_member_cannot_create_loan_for_another_member(): void
    {
        Storage::fake('local');

        $memberA = $this->createUser(
            'member',
            'Member Request Owner'
        );

        $memberB = $this->createUser(
            'member',
            'Member Forged Target'
        );

        MemberProfile::create([
            'user_id' => $memberA->id,
            'member_number' => 'TEST-' . uniqid(),
        ]);

        $payload = [
            'user_id' => $memberB->id,

            'requested_amount' => '1800000',

            'purpose_category' => 'consumer',
            'business_type' => null,
            'business_type_other' => null,
            'purpose_description' => 'Pengujian otorisasi user_id.',

            'term_months' => 12,
            'repayment_type' => 'monthly',

            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 1,

            'other_monthly_income' => '0',
            'net_monthly_income' => '3000000',

            'salary_slip' => UploadedFile::fake()->create(
                'salary-slip.pdf',
                100,
                'application/pdf'
            ),
        ];

        $this->actingAs($memberA);

        $response = $this->post(
            route('member.loans.store'),
            $payload
        );

        $response->assertRedirect(
            route('member.loans.index')
        );

        $loan = Loan::query()
            ->latest('id')
            ->first();

        $this->assertNotNull($loan);

        $this->assertSame(
            $memberA->id,
            $loan->user_id
        );

        $this->assertNotSame(
            $memberB->id,
            $loan->user_id
        );
    }

    public function test_member_only_sees_own_loans_in_loan_list(): void
    {
        $memberA = $this->createUser(
            'member',
            'Member List Owner'
        );

        $memberB = $this->createUser(
            'member',
            'Member List Other'
        );

        $loanA = $this->createLoan($memberA);
        $loanB = $this->createLoan($memberB);

        $this->actingAs($memberA);

        $response = $this->get(
            route('member.loans.index')
        );

        $response->assertOk();

        $response->assertSee(
            $loanA->code
        );

        $response->assertDontSee(
            $loanB->code
        );
    }

    public function test_non_member_cannot_access_member_loan_area(): void
    {
        $analyst = $this->createUser(
            'analyst_manager',
            'Analyst Member Route Test'
        );

        $this->actingAs($analyst);

        $indexResponse = $this->get(
            route('member.loans.index')
        );

        $createResponse = $this->get(
            route('member.loans.create')
        );

        $indexResponse->assertForbidden();
        $createResponse->assertForbidden();
    }

    public function test_inactive_member_cannot_access_member_loan_area(): void
    {
        $member = $this->createUser(
            'member',
            'Inactive Member Route Test'
        );

        $member->update([
            'status' => 'inactive',
        ]);

        $this->actingAs($member);

        $response = $this->get(
            route('member.loans.index')
        );

        $response->assertRedirect(
            route('login')
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
            'code' => 'KOPKARMADA/TEST-MEMBER-AUTH/' .
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
            'purpose_description' => 'Pengujian otorisasi member.',

            'term_months' => 12,
            'repayment_type' => 'monthly',
            'monthly_installment' => null,

            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 1,

            'other_monthly_income' => 0,
            'net_monthly_income' => 3000000,

            'salary_slip' => 'private/test/salary-slip/test.pdf',

            'interest_rate' => 8,
            'approved_amount' => null,

            'status' => 'submitted',
        ]);
    }
}
