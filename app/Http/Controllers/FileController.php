<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    /**
     * Menampilkan dokumen UserDetail.
     *
     * Member normal hanya boleh melihat dokumennya sendiri.
     * Petugas internal hanya boleh melihat dokumen dalam konteks pekerjaan.
     */
    public function userDetail(User $user, string $field): BinaryFileResponse
    {
        $allowedFields = [
            'foto_diri',
            'file_ktp',
            'file_kk',
        ];

        abort_unless(
            in_array($field, $allowedFields, true),
            404
        );

        $this->authorizeUserDocumentAccess($user);

        $file = UserDetail::where('user_id', $user->id)
            ->value($field);

        abort_unless($file, 404);

        return $this->serve($file);
    }

    /**
     * Menampilkan dokumen pinjaman.
     */
    public function pinjaman(Pinjaman $pinjaman, string $field): BinaryFileResponse
    {
        $allowedFields = [
            'slip_gaji',
            'bukti_file',
        ];

        abort_unless(
            in_array($field, $allowedFields, true),
            404
        );

        $this->authorizeLoanDocumentAccess($pinjaman);

        if ($field === 'slip_gaji') {
            $file = $pinjaman->slip_gaji;
        } else {
            $file = $pinjaman->pinjaman_agunan?->bukti_file;
        }

        abort_unless($file, 404);

        return $this->serve($file);
    }

    /**
     * Authorization dokumen profil/nasabah.
     */
    private function authorizeUserDocumentAccess(User $targetUser): void
    {
        $user = auth()->user();

        // Nasabah normal hanya boleh melihat dokumennya sendiri.
        if ($user->id === $targetUser->id && $user->isAnggota() && $user->isNormal()) {
            return;
        }

        // Jabatan yang memang membutuhkan akses dokumen nasabah.
        if (
            $user->isAdmin() ||
            $user->isKetua() ||
            $user->isSekretaris() ||
            $user->isBendahara() ||
            $user->isManajer() ||
            $user->isPetugas()
        ) {
            return;
        }

        abort(403);
    }

    /**
     * Authorization dokumen pinjaman.
     */
    private function authorizeLoanDocumentAccess(Pinjaman $pinjaman): void
    {
        $user = auth()->user();

        // Nasabah normal hanya boleh melihat pinjamannya sendiri.
        if (
            $user->isAnggota() &&
            $user->isNormal()
        ) {
            abort_unless(
                $pinjaman->user_id === $user->id,
                403
            );

            return;
        }

        // Jabatan internal yang memang membutuhkan akses dokumen pinjaman.
        if (
            $user->isAdmin() ||
            $user->isKetua() ||
            $user->isSekretaris() ||
            $user->isBendahara() ||
            $user->isManajer() ||
            $user->isPetugas()
        ) {
            return;
        }

        abort(403);
    }

    /**
     * Mengirim file dari private storage.
     */
    private function serve(string $file): BinaryFileResponse
    {
        // File baru wajib berupa relative path di disk local.
        $path = ltrim($file, '/');

        abort_unless(
            Storage::disk('local')->exists($path),
            404
        );

        return response()->file(
            Storage::disk('local')->path($path)
        );
    }
}
