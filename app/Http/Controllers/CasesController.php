<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\ProcesoService;

class CasesController extends Controller
{
    protected $procesoService;

    public function __construct(ProcesoService $procesoService)
    {
        $this->middleware('auth');
        $this->procesoService = $procesoService;
    }

    public function index()
    {
        try {
            $cases = CaseModel::with('user')
                ->where('user_id', auth()->id())
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
        // Verificar que el caso pertenece al usuario actual
        if ($case->user_id !== auth()->id()) {
            abort(403, 'No tienes permisos para ver este caso');
        }
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

    /**
     * Muestra el formulario de búsqueda de procesos
     *
     * @return \Illuminate\View\View
     */
    public function buscar()
    {
        return view('cases.buscar');
    }

    /**
     * Busca un proceso por número de radicación
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscarProceso(Request $request)
    {
        $request->validate([
            'numero_radicacion' => 'required|string|max:100'
        ]);

        try {
            $numeroRadicacion = $request->input('numero_radicacion');

            // Consultar el proceso
            $resultado = $this->procesoService->consultarProceso($numeroRadicacion);

            if (!$resultado['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['error'] ?? 'Error al consultar el proceso.'
                ], 400);
            }

            // Log para depuración
            \Log::debug('Respuesta completa de la API:', $resultado);
            \Log::debug('Datos de la respuesta:', ['data' => $resultado['data'] ?? null]);

            // Verificar si hay datos de procesos en la respuesta
            if (empty($resultado['data']) || !isset($resultado['data']['procesos']) || empty($resultado['data']['procesos'])) {
                \Log::debug('No se encontraron procesos en la respuesta');
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron procesos con el número de radicación proporcionado.'
                ], 404);
            }

            // Obtener el primer proceso
            $procesoData = $resultado['data']['procesos'][0];

            // Log para depuración
            \Log::debug('Datos del proceso:', $procesoData);

            // Verificar si hay sujetos procesales
            $sujetosProcesales = $procesoData['sujetosProcesales'] ?? [];
            if (empty($sujetosProcesales)) {
                // Intentar obtener los sujetos procesales de otra ubicación si es necesario
                $sujetosProcesales = $procesoData['partes'] ?? $procesoData['sujetos'] ?? [];
                \Log::debug('Sujetos procesales alternativos:', $sujetosProcesales);
            }

            // Formatear los datos del proceso para la respuesta
            $procesoFormateado = [
                'id' => $procesoData['idProceso'] ?? $procesoData['id'] ?? null,
                'numero_radicacion' => $procesoData['numero'] ?? $procesoData['numeroRadicacion'] ?? $numeroRadicacion,
                'tipo_proceso' => $procesoData['tipoProceso'] ?? $procesoData['tipo'] ?? null,
                'despacho' => $procesoData['despacho'] ?? $procesoData['juzgado'] ?? null,
                'departamento' => $procesoData['departamento'] ?? null,
                'ciudad' => $procesoData['ciudad'] ?? null,
                'fecha_proceso' => $procesoData['fechaProceso'] ?? $procesoData['fechaInicio'] ?? null,
                'sujetos_procesales' => $sujetosProcesales
            ];

            \Log::debug('Proceso formateado:', $procesoFormateado);

            // Guardar el proceso en la base de datos
            $procesos = $this->procesoService->procesarYGuardarRespuesta(
                $resultado['data'],
                auth()->id()
            );

            // Si hubo un error al guardar, devolver el error
            if (!$procesos['success']) {
                return response()->json($procesos);
            }

            // Devolver los datos formateados del proceso junto con la respuesta completa para depuración
            return response()->json([
                'success' => true,
                'message' => 'Proceso encontrado correctamente',
                'data' => [$procesoFormateado],
                'debug' => [
                    'respuesta_api' => $resultado,
                    'proceso_data' => $procesoData,
                    'sujetos_procesales' => $sujetosProcesales
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en buscarProceso: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guarda un caso a partir de los datos de un proceso consultado
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function guardarProceso(Request $request)
    {
        \Log::info('Iniciando guardarProceso', $request->all());
        
        try {
            $validated = $request->validate([
                'proceso_id' => 'required|string',
                'llave_proceso' => 'nullable|string|max:255',
                'numero_radicacion' => 'required|string',
                'tipo_proceso' => 'nullable|string',
                'departamento' => 'nullable|string',
                'ciudad' => 'nullable|string',
                'despacho' => 'nullable|string',
                'fecha_proceso' => 'nullable|date',
                'sujetos_procesales' => 'nullable|array',
            ]);
            
            \Log::info('Datos validados', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Error de validación:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            // Verificar si ya existe un caso con este proceso
            $casoExistente = CaseModel::where('proceso_id', $request->proceso_id)
                ->orWhere('llave_proceso', $request->llave_proceso)
                ->first();

            if ($casoExistente) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ya existe un caso con este proceso.',
                    'case_id' => $casoExistente->id
                ]);
            }

            // Crear un nuevo caso con los datos del proceso
            $caso = new CaseModel();
            $caso->user_id = auth()->id();
            $caso->proceso_id = $request->proceso_id;
            $caso->llave_proceso = $request->llave_proceso;
            $caso->radicado = $request->numero_radicacion;
            $caso->tipo_proceso = $request->tipo_proceso;
            $caso->fecha_radicacion = $request->fecha_proceso;
            $caso->demandante = $this->extraerDemandante($request->sujetos_procesales);
            $caso->demandado = $this->extraerDemandado($request->sujetos_procesales);
            $caso->despacho = $request->despacho;
            $caso->departamento = $request->departamento;
            $caso->ciudad = $request->ciudad;
            $caso->save();

            return response()->json([
                'success' => true,
                'message' => 'Caso guardado exitosamente.',
                'case_id' => $caso->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al guardar el caso: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el caso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extrae el nombre del demandante de los sujetos procesales
     *
     * @param  array  $sujetosProcesales
     * @return string|null
     */
    protected function extraerDemandante($sujetosProcesales)
    {
        if (!is_array($sujetosProcesales)) {
            return null;
        }

        $demandante = collect($sujetosProcesales)->first(function ($sujeto) {
            return isset($sujeto['tipoSujeto']) &&
                   in_array(strtolower($sujeto['tipoSujeto']), ['demandante', 'actor', 'demandante acumulado']);
        });

        return $demandante['nombre'] ?? null;
    }

    /**
     * Extrae el nombre del demandado de los sujetos procesales
     *
     * @param  array  $sujetosProcesales
     * @return string|null
     */
    protected function extraerDemandado($sujetosProcesales)
    {
        if (!is_array($sujetosProcesales)) {
            return null;
        }

        $demandado = collect($sujetosProcesales)->first(function ($sujeto) {
            return isset($sujeto['tipoSujeto']) &&
                   in_array(strtolower($sujeto['tipoSujeto']), ['demandado', 'demandado principal', 'demandado acumulado']);
        });

        return $demandado['nombre'] ?? null;
    }

}
