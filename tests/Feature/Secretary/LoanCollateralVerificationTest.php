<?php

namespace Tests\Feature\Secretary;

use App\Models\Loan;
use App\Models\LoanCollateral;
use App\Models\LoanDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanCollateralVerificationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_secretary_can_verify_pending_collateral(): void
    {
        $member = $this->createUser('member');
        $secretary = $this->createUser('secretary');

        $loan = $this->createLoan($member, 30000000);

        $collateral = LoanCollateral::create([
            'loan_id' => $loan->id,
            'type' => 'bpkb',
            'description' => 'BPKB kendaraan untuk pengujian.',
            'ownership_status' => 'self',
            'ownership_proof' => 'bpkb',
            'proof_file' => 'private/test/collateral.pdf',
            'status' => 'pending',
        ]);

        $this->actingAs($secretary);

        $response = $this->post(
            route('secretary.loans.collaterals.verify', [
                'loan' => $loan,
                'collateral' => $collateral,
            ]),
            [
                'notes' => 'Dokumen agunan sesuai dan dapat diverifikasi.',
            ]
        );

        $response->assertSessionHas('success');

        $collateral->refresh();

        $this->assertSame('verified', $collateral->status);
        $this->assertSame($secretary->id, $collateral->verified_by);
        $this->assertNotNull($collateral->verified_at);
        $this->assertSame(
            'Dokumen agunan sesuai dan dapat diverifikasi.',
            $collateral->verification_notes
        );
    }

    public function test_secretary_can_reject_pending_collateral_with_required_notes(): void
    {
        $member = $this->createUser('member');
        $secretary = $this->createUser('secretary');

        $loan = $this->createLoan($member, 30000000);

        $collateral = LoanCollateral::create([
            'loan_id' => $loan->id,
            'type' => 'bpkb',
            'description' => 'BPKB kendaraan untuk pengujian.',
            'ownership_status' => 'self',
            'ownership_proof' => 'bpkb',
            'proof_file' => 'private/test/collateral.pdf',
            'status' => 'pending',
        ]);

        $this->actingAs($secretary);

        $response = $this->post(
            route('secretary.loans.collaterals.reject', [
                'loan' => $loan,
                'collateral' => $collateral,
            ]),
            [
                'notes' => 'Bukti kepemilikan belum sesuai.',
            ]
        );

        $response->assertSessionHas('success');

        $collateral->refresh();

        $this->assertSame('rejected', $collateral->status);
        $this->assertSame($secretary->id, $collateral->verified_by);
        $this->assertNotNull($collateral->verified_at);
        $this->assertSame(
            'Bukti kepemilikan belum sesuai.',
            $collateral->verification_notes
        );
    }

    public function test_secretary_cannot_verify_collateral_before_loan_is_approved(): void
    {
        $member = $this->createUser('member');
        $secretary = $this->createUser('secretary');

        $loan = $this->createLoan($member, 30000000);
        $loan->update(['status' => 'waiting_chairman_approval']);

        $collateral = LoanCollateral::create([
            'loan_id' => $loan->id,
            'type' => 'bpkb',
            'description' => 'BPKB kendaraan untuk pengujian.',
            'ownership_status' => 'self',
            'ownership_proof' => 'bpkb',
            'proof_file' => 'private/test/collateral.pdf',
            'status' => 'pending',
        ]);

        $this->actingAs($secretary);

        $response = $this->post(
            route('secretary.loans.collaterals.verify', [
                'loan' => $loan,
                'collateral' => $collateral,
            ])
        );

        $response->assertNotFound();

        $collateral->refresh();

        $this->assertSame('pending', $collateral->status);
        $this->assertNull($collateral->verified_by);
        $this->assertNull($collateral->verified_at);
    }

    public function test_secretary_can_see_pending_collateral_on_loan_detail(): void
    {
        $member = $this->createUser('member');
        $secretary = $this->createUser('secretary');

        $loan = $this->createLoan($member, 30000000);

        LoanCollateral::create([
            'loan_id' => $loan->id,
            'type' => 'bpkb',
            'description' => 'BPKB kendaraan untuk pengujian.',
            'ownership_status' => 'self',
            'ownership_proof' => 'bpkb',
            'proof_file' => 'private/test/collateral.pdf',
            'status' => 'pending',
        ]);

        $this->actingAs($secretary);

        $response = $this->get(
            route('secretary.loans.show', $loan)
        );

        $response->assertOk();
        $response->assertSee('Agunan');
        $response->assertSee('Verifikasi Agunan');
        $response->assertSee('Perlu Diperbaiki');
    }

    private function createUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role) . ' Collateral Test',
            'nickname' => $role . '_collateral_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'active',
        ]);
    }

    private function createLoan(User $member, float $amount): Loan
    {
        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        return Loan::create([
            'code' => 'KOPKARMADA/COLLATERAL/' . now()->format('YmdHis') . '/' . $sequenceNumber,
            'sequence_number' => $sequenceNumber,
            'user_id' => $member->id,
            'submitted_at' => now(),
            'requested_amount' => $amount,
            'purpose_category' => 'consumer',
            'purpose_description' => 'Pengujian agunan.',
            'term_months' => 12,
            'repayment_type' => 'monthly',
            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 1,
            'other_monthly_income' => 0,
            'net_monthly_income' => 5000000,
            'salary_slip' => 'private/test/salary-slip.pdf',
            'interest_rate' => 8,
            'approved_amount' => $amount,
            'status' => 'approved',
        ]);
    }
}
