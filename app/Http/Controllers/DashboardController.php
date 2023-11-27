<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Cache;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $total = Cache::sear('total_patients', static fn () => Patient::count('id'));

        return response()->json([
            'total_patients' => $total
        ]);
    }
}
