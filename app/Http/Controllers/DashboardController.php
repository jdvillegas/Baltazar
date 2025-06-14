<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaseModel;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Si es primera vez que se inicia sesión, establecer fechas de trial
        if (!$user->trial_start_date) {
            $user->trial_start_date = now();
            $user->trial_end_date = now()->addDays(90);
            $user->save();
        }

        // Convertir fechas a Carbon si son strings
        $trialEndDate = $user->trial_end_date instanceof \Carbon\Carbon 
            ? $user->trial_end_date 
            : \Carbon\Carbon::parse($user->trial_end_date);

        // Calcular días restantes
        $daysRemaining = $trialEndDate->diffInDays(now());
        
        // Determinar color basado en días restantes
        $daysColor = match(true) {
            $daysRemaining <= 10 => 'danger',
            $daysRemaining <= 20 => 'warning',
            default => 'success'
        };

        // Contar casos abiertos
        $openCases = CaseModel::where('status', 'pendiente')
            ->orWhere('status', 'en_proceso')
            ->orWhere('status', 'anulado')
            ->count();

        return view('dashboard', [
            'daysRemaining' => $daysRemaining,
            'daysColor' => $daysColor,
            'openCases' => $openCases,
            'maxOpenCases' => $user->max_open_cases
        ]);
    }
}
