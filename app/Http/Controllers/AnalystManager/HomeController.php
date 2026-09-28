<?php

namespace App\Http\Controllers\AnalystManager;

use App\Http\Controllers\Controller;
use App\Models\Loan;

class HomeController extends Controller
{
    public function index()
    {
        $this->authorizeRole();

        $submitted = Loan::where('status', 'submitted')->count();

        $underAnalysis = Loan::where('status', 'under_analysis')->count();

        $waitingTreasurer = Loan::where(
            'status',
            'waiting_treasurer_review'
        )->count();

        $waitingChairman = Loan::where(
            'status',
            'waiting_chairman_approval'
        )->count();

        $approved = Loan::where('status', 'approved')->count();

        return view('analyst-manager.home', compact(
            'submitted',
            'underAnalysis',
            'waitingTreasurer',
            'waitingChairman',
            'approved'
        ));
    }

    private function authorizeRole(): void
    {
        abort_unless(
            auth()->check()
                && auth()->user()->isActive()
                && auth()->user()->isAnalystManager(),
            403
        );
    }
}
