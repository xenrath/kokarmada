<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class LoanSettingController extends Controller
{
    public function edit()
    {
        $this->authorizeRole();

        $settings = [
            'top_up_minimum_amount' => Setting::where(
                'key',
                'top_up_minimum_amount'
            )->value('value'),

            'loan_maximum_amount' => Setting::where(
                'key',
                'loan_maximum_amount'
            )->value('value'),

            'loan_capacity_threshold_percent' => Setting::where(
                'key',
                'loan_capacity_threshold_percent'
            )->value('value'),
        ];

        if ($settings['loan_capacity_threshold_percent'] === null) {
            $settings['loan_capacity_threshold_percent'] = '40';
        }

        return view(
            'admin.loan-settings.edit',
            compact('settings')
        );
    }

    public function update(Request $request)
    {
        $this->authorizeRole();

        $validated = $request->validate([
            'top_up_minimum_amount' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'loan_maximum_amount' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'loan_capacity_threshold_percent' => [
                'required',
                'numeric',
                'gte:0',
                'lte:100',
            ],
        ], [
            'top_up_minimum_amount.required' =>
                'Nominal minimum Top Up wajib diisi.',
            'top_up_minimum_amount.numeric' =>
                'Nominal minimum Top Up harus berupa angka.',
            'top_up_minimum_amount.gt' =>
                'Nominal minimum Top Up harus lebih dari Rp0.',

            'loan_maximum_amount.required' =>
                'Batas maksimum pinjaman wajib diisi.',
            'loan_maximum_amount.numeric' =>
                'Batas maksimum pinjaman harus berupa angka.',
            'loan_maximum_amount.gt' =>
                'Batas maksimum pinjaman harus lebih dari Rp0.',

            'loan_capacity_threshold_percent.required' =>
                'Ambang kapasitas wajib diisi.',
            'loan_capacity_threshold_percent.numeric' =>
                'Ambang kapasitas harus berupa angka.',
            'loan_capacity_threshold_percent.gte' =>
                'Ambang kapasitas tidak boleh kurang dari 0%.',
            'loan_capacity_threshold_percent.lte' =>
                'Ambang kapasitas tidak boleh lebih dari 100%.',
        ]);

        if (
            (float) $validated['loan_maximum_amount']
            < (float) $validated['top_up_minimum_amount']
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'loan_maximum_amount' =>
                        'Batas maksimum pinjaman tidak boleh lebih kecil dari nominal minimum Top Up.',
                ]);
        }

        $descriptions = [
            'top_up_minimum_amount' =>
                'Nominal tambahan minimum yang dapat diajukan saat Top Up.',
            'loan_maximum_amount' =>
                'Batas maksimum total pokok kontrak pinjaman, termasuk Top Up.',
            'loan_capacity_threshold_percent' =>
                'Ambang kapasitas keuangan sebagai indikator analisis kemampuan pembayaran.',
        ];

        foreach ($descriptions as $key => $description) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => (string) $validated[$key],
                    'description' => $description,
                ]
            );
        }

        return redirect()
            ->route('admin.loan-settings.edit')
            ->with(
                'success',
                'Pengaturan pinjaman dan Top Up berhasil diperbarui.'
            );
    }

    private function authorizeRole(): void
    {
        abort_unless(
            auth()->check()
                && auth()->user()->isActive()
                && auth()->user()->isAdmin(),
            403
        );
    }
}
