<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\Loan;

class HomeController extends Controller
{
    public function index()
    {
        $this->authorizeRole();

        $waitingReview = Loan::where(
            'status',
            'waiting_treasurer_review'
        )->count();

        $waitingChairman = Loan::where(
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

        return view('treasurer.home', compact(
            'waitingReview',
            'waitingChairman',
            'approved',
            'disbursed'
        ));
    }

    private function authorizeRole(): void
    {
        abort_unless(
            auth()->check()
                && auth()->user()->isActive()
                && auth()->user()->isTreasurer(),
            403
        );
    }
}
