<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CasesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:cases.view')->only('index');
        $this->middleware('permission:cases.create')->only('create', 'store');
        $this->middleware('permission:cases.edit')->only('edit', 'update');
        $this->middleware('permission:cases.delete')->only('destroy');
    }

    public function index()
    {
        try {
            $cases = CaseModel::with('user')
                ->get();
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

            $user = auth()->user();
            $openCases = CaseModel::getActiveCount($user->id);

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
            $case->status = 'anulado';
            $case->anulled_at = now();
            $case->save();
            DB::commit();
            return redirect()->route('cases.index')->with('success', 'Caso anulado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al anular el caso: ' . $e->getMessage());
        }
    }
}
