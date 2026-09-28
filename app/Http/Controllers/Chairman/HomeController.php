<?php

namespace App\Http\Controllers\Chairman;

use App\Http\Controllers\Controller;
use App\Models\Loan;

class HomeController extends Controller
{
    public function index()
    {
        $this->authorizeRole();

        $waitingApproval = Loan::where(
            'status',
            'waiting_chairman_approval'
        )->count();

        $approved = Loan::where(
            'status',
            'approved'
        )->count();

        $disbursed = Loan::where(
            'status',
            'disbursed'
        )->count();

        return view('chairman.home', compact(
            'waitingApproval',
            'approved',
            'disbursed'
        ));
    }

    private function authorizeRole(): void
    {
        abort_unless(
            auth()->check()
                && auth()->user()->isActive()
                && auth()->user()->isChairman(),
            403
        );
    }
}
