<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class ProfileController extends Controller
{
    public function index(): View
    {
        $this->authorizeRole();

        $user = auth()->user()->load('memberProfile');

        return view('member.profile.index', compact('user'));
    }

    public function edit(): View
    {
        $this->authorizeRole();

        $user = auth()->user()->load('memberProfile');

        return view('member.profile.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizeRole();

        $user = auth()->user()->load('memberProfile');
        $profile = $user->memberProfile;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
                'unique:users,phone,' . $user->id,
            ],

            'gender' => [
                'required',
                'in:L,P',
            ],

            'national_id' => [
                'nullable',
                'string',
                'max:30',
                'unique:member_profiles,national_id,' .
                    ($profile?->id ?? 'NULL'),
            ],

            'national_id_expiry' => [
                'nullable',
                'date',
            ],

            'national_id_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],

            'family_card_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],

            'photo_file' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],

            'birth_place' => [
                'nullable',
                'string',
                'max:100',
            ],

            'birth_date' => [
                'nullable',
                'date',
            ],

            'address' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:10',
            ],

            'occupation' => [
                'nullable',
                'string',
                'max:100',
            ],

            'tax_id' => [
                'nullable',
                'string',
                'max:30',
            ],

            'mother_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'marital_status' => [
                'nullable',
                'string',
                'max:30',
            ],

            'spouse_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'spouse_occupation' => [
                'nullable',
                'string',
                'max:100',
            ],

            'bank_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'bank_account_number' => [
                'nullable',
                'string',
                'max:50',
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor HP / WhatsApp wajib diisi.',
            'phone.unique' => 'Nomor HP / WhatsApp sudah digunakan.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Jenis kelamin tidak valid.',
            'national_id.unique' => 'Nomor KTP sudah digunakan.',
            'national_id_file.mimes' =>
            'File KTP harus berupa PDF, JPG, JPEG, atau PNG.',
            'national_id_file.max' =>
            'Ukuran file KTP maksimal 2 MB.',
            'family_card_file.mimes' =>
            'File KK harus berupa PDF, JPG, JPEG, atau PNG.',
            'family_card_file.max' =>
            'Ukuran file KK maksimal 2 MB.',
            'photo_file.image' =>
            'Foto profil harus berupa gambar.',
            'photo_file.mimes' =>
            'Foto profil harus berupa JPG, JPEG, atau PNG.',
            'photo_file.max' =>
            'Ukuran foto profil maksimal 2 MB.',
        ]);

        $storedFiles = [];
        $oldFiles = [];

        try {
            DB::transaction(function () use (
                $request,
                $user,
                $profile,
                $validated,
                &$storedFiles,
                &$oldFiles
            ) {
                $user->update([
                    'name' => trim($validated['name']),
                    'phone' => trim($validated['phone']),
                    'gender' => $validated['gender'],
                ]);

                if ($request->hasFile('national_id_file')) {
                    $path = $request->file('national_id_file')->store(
                        'private/member-profiles/' . $user->id . '/ktp',
                        'local'
                    );

                    $storedFiles[] = $path;

                    if ($profile?->national_id_file) {
                        $oldFiles[] = $profile->national_id_file;
                    }
                }

                if ($request->hasFile('family_card_file')) {
                    $path = $request->file('family_card_file')->store(
                        'private/member-profiles/' . $user->id . '/kk',
                        'local'
                    );

                    $storedFiles[] = $path;

                    if ($profile?->family_card_file) {
                        $oldFiles[] = $profile->family_card_file;
                    }
                }

                if ($request->hasFile('photo_file')) {
                    $path = $request->file('photo_file')->store(
                        'private/member-profiles/' . $user->id . '/photo',
                        'local'
                    );

                    $storedFiles[] = $path;

                    if ($profile?->photo_file) {
                        $oldFiles[] = $profile->photo_file;
                    }
                }

                $profileData = [
                    'national_id' =>
                    $validated['national_id'] ?? null,

                    'national_id_expiry' =>
                    $validated['national_id_expiry'] ?? null,

                    'birth_place' =>
                    trim($validated['birth_place'] ?? '') ?: null,

                    'birth_date' =>
                    $validated['birth_date'] ?? null,

                    'address' =>
                    trim($validated['address'] ?? '') ?: null,

                    'postal_code' =>
                    trim($validated['postal_code'] ?? '') ?: null,

                    'occupation' =>
                    trim($validated['occupation'] ?? '') ?: null,

                    'tax_id' =>
                    trim($validated['tax_id'] ?? '') ?: null,

                    'mother_name' =>
                    trim($validated['mother_name'] ?? '') ?: null,

                    'marital_status' =>
                    $validated['marital_status'] ?? null,

                    'spouse_name' =>
                    trim($validated['spouse_name'] ?? '') ?: null,

                    'spouse_occupation' =>
                    trim($validated['spouse_occupation'] ?? '') ?: null,

                    'bank_name' =>
                    trim($validated['bank_name'] ?? '') ?: null,

                    'bank_account_number' =>
                    trim($validated['bank_account_number'] ?? '') ?: null,
                ];

                if (isset($storedFiles[0]) && $request->hasFile('national_id_file')) {
                    $profileData['national_id_file'] = $storedFiles[0];
                }

                if ($request->hasFile('family_card_file')) {
                    $familyCardPath = collect($storedFiles)
                        ->filter(fn($path) => str_contains($path, '/kk/'))
                        ->last();

                    $profileData['family_card_file'] = $familyCardPath;
                }

                if ($request->hasFile('photo_file')) {
                    $photoPath = collect($storedFiles)
                        ->filter(fn($path) => str_contains($path, '/photo/'))
                        ->last();

                    $profileData['photo_file'] = $photoPath;
                }

                if ($profile) {
                    $profile->update($profileData);
                } else {
                    $profileData['user_id'] = $user->id;
                    $profileData['member_number'] =
                        'KOPKARMADA-' .
                        str_pad(
                            (string) $user->id,
                            6,
                            '0',
                            STR_PAD_LEFT
                        );

                    MemberProfile::create($profileData);
                }
            });

            foreach ($oldFiles as $path) {
                Storage::disk('local')->delete($path);
            }

            return redirect()
                ->route('member.profile.index')
                ->with(
                    'success',
                    'Profil berhasil diperbarui.'
                );
        } catch (Throwable $e) {
            foreach ($storedFiles as $path) {
                Storage::disk('local')->delete($path);
            }

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Profil gagal diperbarui. Silakan coba lagi.'
                );
        }
    }

    private function authorizeRole(): void
    {
        abort_unless(
            auth()->check()
                && auth()->user()->isActive()
                && auth()->user()->isMember(),
            403
        );
    }
}
