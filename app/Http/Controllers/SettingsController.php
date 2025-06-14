<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        // Simulando datos de configuración
        $settings = [
            'company_name' => 'Baltazar',
            'timezone' => 'America/Bogota',
            'email_notifications' => false,
            'theme' => 'light',
            'language' => 'es',
        ];
        
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Validación de datos
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'timezone' => 'required|string',
            'email_notifications' => 'required|boolean',
            'theme' => 'required|string|in:light,dark',
            'language' => 'required|string|in:es,en',
        ]);

        // Aquí iría la lógica para guardar los cambios
        // Por ahora solo redirigimos con mensaje de éxito
        return redirect()->route('settings.index')->with('success', 'Configuración actualizada exitosamente');
    }
}
