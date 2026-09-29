<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\LoanDocument;

class HomeController extends Controller
{
    public function index()
    {
        $this->authorizeRole();

        $pendingDocuments = LoanDocument::query()
            ->whereIn('document_type', [
                'approval_letter',
                'credit_agreement',
            ])
            ->whereNotNull('file_path')
            ->where('status', 'pending')
            ->count();

        $verifiedDocuments = LoanDocument::query()
            ->whereIn('document_type', [
                'approval_letter',
                'credit_agreement',
            ])
            ->where('status', 'verified')
            ->count();

        $rejectedDocuments = LoanDocument::query()
            ->whereIn('document_type', [
                'approval_letter',
                'credit_agreement',
            ])
            ->where('status', 'rejected')
            ->count();

        $loansWaitingVerification = LoanDocument::query()
            ->whereIn('document_type', [
                'approval_letter',
                'credit_agreement',
            ])
            ->whereNotNull('file_path')
            ->where('status', 'pending')
            ->distinct('loan_id')
            ->count('loan_id');

        return view(
            'secretary.home',
            compact(
                'pendingDocuments',
                'verifiedDocuments',
                'rejectedDocuments',
                'loansWaitingVerification'
            )
        );
    }

    private function authorizeRole(): void
    {
        abort_unless(
            auth()->check()
                && auth()->user()->isActive()
                && auth()->user()->isSecretary(),
            403
        );
    }
}
