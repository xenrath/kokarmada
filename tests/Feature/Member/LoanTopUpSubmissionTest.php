<?php

namespace Tests\Feature\Member;

use App\Models\Account;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanDisbursement;
use App\Models\LoanProcess;
use App\Models\MemberProfile;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LoanTopUpSubmissionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_member_can_submit_top_up_over_http(): void
    {
        Storage::fake('local');

        $this->configureSettings();

        $member = $this->createMember();
        $this->createUser('analyst_manager');
        $oldLoan = $this->createEligibleLoan($member);

        $response = $this->actingAs($member)->post(
            route('member.loans.top-up.store', $oldLoan),
            $this->basePayload([
                'salary_slip' => UploadedFile::fake()->create(
                    'salary-latest.pdf',
                    100,
                    'application/pdf'
                ),
            ])
        );

        $response->assertRedirect(
            route('member.loans.index')
        );

        $newLoan = Loan::query()
            ->where('user_id', $member->id)
            ->where('loan_type', 'top_up')
            ->firstOrFail();

        $this->assertSame(
            $oldLoan->id,
            $newLoan->top_up_of_loan_id
        );

        $this->assertSame(
            2500000.00,
            (float) $newLoan->requested_amount
        );

        Storage::disk('local')->assertExists(
            $newLoan->salary_slip
        );

        $this->assertDatabaseHas('loan_processes', [
            'loan_id' => $newLoan->id,
            'user_id' => $member->id,
            'role' => 'member',
            'action' => 'top_up_submitted',
        ]);

        $this->assertDatabaseHas('activities', [
            'subject_type' => Loan::class,
            'subject_id' => $newLoan->id,
            'action' => 'loan_top_up_submitted',
        ]);

        $this->assertSame(
            1,
            Notification::query()
                ->where('reference_type', Loan::class)
                ->where('reference_id', $newLoan->id)
                ->where('type', 'loan_submitted')
                ->count()
        );

    }

    public function test_guest_cannot_submit_top_up(): void
    {
        $member = $this->createMember();
        $oldLoan = $this->createEligibleLoan($member);

        $response = $this->post(
            route('member.loans.top-up.store', $oldLoan)
        );

        $response->assertRedirect(
            route('login')
        );
    }

    public function test_non_member_cannot_submit_top_up(): void
    {
        $member = $this->createMember();
        $admin = $this->createUser('admin');
        $oldLoan = $this->createEligibleLoan($member);

        $response = $this->actingAs($admin)->post(
            route('member.loans.top-up.store', $oldLoan),
            $this->basePayload([
                'salary_slip' => UploadedFile::fake()->create(
                    'salary.pdf',
                    100,
                    'application/pdf'
                ),
            ])
        );

        $response->assertForbidden();

        $this->assertDatabaseMissing('loans', [
            'user_id' => $admin->id,
            'loan_type' => 'top_up',
        ]);
    }

    public function test_member_cannot_submit_top_up_against_another_members_loan(): void
    {
        $member = $this->createMember();
        $otherMember = $this->createMember();
        $oldLoan = $this->createEligibleLoan($otherMember);

        $response = $this->actingAs($member)->post(
            route('member.loans.top-up.store', $oldLoan),
            $this->basePayload([
                'salary_slip' => UploadedFile::fake()->create(
                    'salary.pdf',
                    100,
                    'application/pdf'
                ),
            ])
        );

        $response->assertNotFound();
    }

    public function test_salary_slip_is_required(): void
    {
        $member = $this->createMember();
        $oldLoan = $this->createEligibleLoan($member);

        $payload = $this->basePayload();
        unset($payload['salary_slip']);

        $response = $this->actingAs($member)->post(
            route('member.loans.top-up.store', $oldLoan),
            $payload
        );

        $response
            ->assertSessionHasErrors('salary_slip');

        $this->assertSame(
            1,
            Loan::query()
                ->where('user_id', $member->id)
                ->count()
        );
    }

    public function test_external_obligations_proof_is_optional(): void
    {
        Storage::fake('local');

        $this->configureSettings();

        $member = $this->createMember();
        $this->createUser('analyst_manager');
        $oldLoan = $this->createEligibleLoan($member);

        $response = $this->actingAs($member)->post(
            route('member.loans.top-up.store', $oldLoan),
            $this->basePayload([
                'declared_external_monthly_obligations' => 250000,
                'declared_external_obligations_note' =>
                    'Pinjaman bank di luar koperasi.',
                'salary_slip' => UploadedFile::fake()->create(
                    'salary.pdf',
                    100,
                    'application/pdf'
                ),
            ])
        );

        $response->assertRedirect(
            route('member.loans.index')
        );

        $newLoan = Loan::query()
            ->where('user_id', $member->id)
            ->where('loan_type', 'top_up')
            ->firstOrFail();

        $this->assertSame(
            250000.00,
            (float) $newLoan->declared_external_monthly_obligations
        );

        $this->assertNull(
            $newLoan->declared_external_obligations_proof
        );
    }

    public function test_top_up_above_25_million_requires_collateral(): void
    {
        Storage::fake('local');

        $member = $this->createMember();
        $this->createUser('analyst_manager');

        $this->setSetting('top_up_minimum_amount', 500000);
        $this->setSetting('loan_maximum_amount', 40000000);
        $this->setSetting('loan_capacity_threshold_percent', 40);
        $this->setSetting('loan_interest_rate', 8);

        $oldLoan = $this->createEligibleLoan(
            $member,
            40000000
        );

        $response = $this->actingAs($member)->post(
            route('member.loans.top-up.store', $oldLoan),
            $this->basePayload([
                'top_up_amount' => 6000000,
                'salary_slip' => UploadedFile::fake()->create(
                    'salary.pdf',
                    100,
                    'application/pdf'
                ),
            ])
        );

        $response->assertSessionHasErrors([
            'collateral_type',
            'ownership_status',
            'ownership_proof',
            'collateral_proof_file',
        ]);

        $this->assertSame(
            1,
            Loan::query()
                ->where('user_id', $member->id)
                ->count()
        );

        Storage::disk('local')->assertDirectoryEmpty(
            'private/top-up'
        );
    }

    public function test_top_up_above_25_million_stores_collateral_file(): void
    {
        Storage::fake('local');

        $member = $this->createMember();
        $this->createUser('analyst_manager');

        $this->setSetting('top_up_minimum_amount', 500000);
        $this->setSetting('loan_maximum_amount', 40000000);
        $this->setSetting('loan_capacity_threshold_percent', 40);

        $oldLoan = $this->createEligibleLoan(
            $member,
            40000000
        );

        $response = $this->actingAs($member)->post(
            route('member.loans.top-up.store', $oldLoan),
            $this->basePayload([
                'top_up_amount' => 6000000,
                'salary_slip' => UploadedFile::fake()->create(
                    'salary.pdf',
                    100,
                    'application/pdf'
                ),
                'collateral_type' => 'bpkb',
                'collateral_description' => 'BPKB kendaraan.',
                'ownership_status' => 'self',
                'ownership_proof' => 'bpkb',
                'collateral_proof_file' =>
                    UploadedFile::fake()->create(
                        'bpkb.pdf',
                        100,
                        'application/pdf'
                    ),
            ])
        );

        $response->assertRedirect(
            route('member.loans.index')
        );

        $newLoan = Loan::query()
            ->where('user_id', $member->id)
            ->where('loan_type', 'top_up')
            ->firstOrFail();

        $collateral = $newLoan
            ->collaterals()
            ->firstOrFail();

        $this->assertSame(
            'pending',
            $collateral->status
        );

        Storage::disk('local')->assertExists(
            $collateral->proof_file
        );
    }

    public function test_ineligible_top_up_is_rejected_before_files_are_stored(): void
    {
        Storage::fake('local');

        $member = $this->createMember();
        $oldLoan = $this->createEligibleLoan($member);

        $oldLoan->installments()
            ->where('installment_number', 1)
            ->update([
                'status' => 'pending',
                'paid_at' => null,
            ]);

        $response = $this->actingAs($member)->post(
            route('member.loans.top-up.store', $oldLoan),
            $this->basePayload([
                'salary_slip' => UploadedFile::fake()->create(
                    'salary.pdf',
                    100,
                    'application/pdf'
                ),
            ])
        );

        $response
            ->assertSessionHasErrors('top_up_amount');

        $this->assertSame(
            1,
            Loan::query()
                ->where('user_id', $member->id)
                ->count()
        );

        Storage::disk('local')->assertDirectoryEmpty(
            'private/top-up'
        );
    }

    private function basePayload(array $overrides = []): array
    {
        return array_merge([
            'top_up_amount' => 1000000,
            'term_months' => 12,
            'purpose_category' => 'consumer',
            'purpose_description' => 'Tambahan kebutuhan keluarga.',
            'work_unit' => 'Unit Baru',
            'position' => 'Tester',
            'employment_duration_years' => 3,
            'net_monthly_income' => 4500000,
            'other_monthly_income' => 0,
            'declared_external_monthly_obligations' => 100000,
            'salary_slip' => UploadedFile::fake()->create(
                'salary.pdf',
                100,
                'application/pdf'
            ),
        ], $overrides);
    }

    private function createMember(): User
    {
        $member = $this->createUser('member');

        MemberProfile::create([
            'user_id' => $member->id,
            'member_number' => 'MP-' . bin2hex(
                random_bytes(8)
            ),
        ]);

        return $member;
    }

    private function createUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role) . ' HTTP Top Up Test',
            'nickname' => 'http_' . substr(
                uniqid(),
                0,
                10
            ),
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

    private function configureSettings(): void
    {
        $this->setSetting('top_up_minimum_amount', 500000);
        $this->setSetting('loan_maximum_amount', 5000000);
        $this->setSetting('loan_capacity_threshold_percent', 40);
        $this->setSetting('loan_interest_rate', 8);
    }

    private function setSetting(string $key, float $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'description' => 'HTTP test setting ' . $key,
            ]
        );
    }

    private function createEligibleLoan(
        User $member,
        float $amount = 3000000
    ): Loan {
        $loan = Loan::create([
            'code' => 'KOPKARMADA/HTTP-TOPUP/' . uniqid(),
            'sequence_number' => ((int) Loan::max('sequence_number')) + 1,
            'user_id' => $member->id,
            'submitted_at' => now()->subMonths(3),
            'loan_type' => 'regular',
            'requested_amount' => $amount,
            'purpose_category' => 'consumer',
            'purpose_description' => 'Pinjaman lama untuk pengujian HTTP Top Up.',
            'term_months' => 12,
            'repayment_type' => 'monthly',
            'monthly_installment' => 280000,
            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 2,
            'net_monthly_income' => 4000000,
            'other_monthly_income' => 500000,
            'declared_external_monthly_obligations' => 100000,
            'salary_slip' => 'private/test/salary-slip.pdf',
            'interest_rate' => 8,
            'approved_amount' => $amount,
            'status' => 'disbursed',
        ]);

        $treasurer = $this->createUser('treasurer');

        $account = Account::create([
            'name' => 'HTTP Top Up Account',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'HTTP-' . uniqid(),
            'account_name' => 'KOPKARMADA TEST',
            'opening_balance' => $amount + 1000000,
            'is_active' => true,
        ]);

        LoanDisbursement::create([
            'loan_id' => $loan->id,
            'treasurer_id' => $treasurer->id,
            'account_id' => $account->id,
            'amount' => $amount,
            'disbursed_at' => now()->subMonths(3),
        ]);

        Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 1,
            'due_date' => now()->subMonth()->startOfDay(),
            'principal_amount' => $amount / 2,
            'interest_amount' => 0,
            'penalty_amount' => 0,
            'total_amount' => $amount / 2,
            'paid_at' => now()->subWeeks(2),
            'status' => 'paid',
        ]);

        Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 2,
            'due_date' => now()->addMonth()->startOfDay(),
            'principal_amount' => $amount / 2,
            'interest_amount' => 0,
            'penalty_amount' => 0,
            'total_amount' => $amount / 2,
            'paid_at' => null,
            'status' => 'pending',
        ]);

        return $loan;
    }
}
