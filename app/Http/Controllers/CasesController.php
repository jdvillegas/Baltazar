<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class CasesController extends Controller
{
    public function __construct()
    {
        // Middleware de autenticación se maneja a nivel de rutas
    }

    public function index()
    {
        try {
            $cases = CaseModel::all();
            return view('cases.index', compact('cases'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar los casos: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('cases.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|string',
            ]);

            DB::beginTransaction();
            $case = CaseModel::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'status' => $validated['status'],
                'user_id' => Auth::id(),
            ]);
            DB::commit();

            return redirect()->route('cases.index')->with('success', 'Caso creado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al crear el caso: ' . $e->getMessage());
        }
    }

    public function show(CaseModel $case)
    {
        return view('cases.show', compact('case'));
    }

    public function edit(CaseModel $case)
    {
        return view('cases.edit', compact('case'));
    }

    public function update(Request $request, CaseModel $case)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|string',
            ]);

            DB::beginTransaction();
            $case->update($validated);
            DB::commit();

            return redirect()->route('cases.index')->with('success', 'Caso actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar el caso: ' . $e->getMessage());
        }
    }

    public function destroy(CaseModel $case)
    {
        try {
            DB::beginTransaction();
            $case->delete();
            DB::commit();

            return redirect()->route('cases.index')->with('success', 'Caso eliminado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar el caso: ' . $e->getMessage());
        }
    }
}
