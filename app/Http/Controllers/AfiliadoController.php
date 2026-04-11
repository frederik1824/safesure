<?php

namespace App\Http\Controllers;

use App\Services\AfiliadoService;
use App\Services\EvidenciaService;
use App\Models\Afiliado;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAfiliadoRequest;
use App\Http\Requests\UpdateAfiliadoRequest;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AfiliadosExport;

class AfiliadoController extends Controller
{
    protected $afiliadoService;
    protected $evidenciaService;
    protected $firebaseSync;

    public function __construct(AfiliadoService $afiliadoService, EvidenciaService $evidenciaService, \App\Services\FirebaseSyncService $firebaseSync)
    {
        $this->afiliadoService = $afiliadoService;
        $this->evidenciaService = $evidenciaService;
        $this->firebaseSync = $firebaseSync;
    }

    /**
     * Verifica duplicados en Firebase en tiempo real
     */
    public function checkDuplicate(Request $request, \App\Services\FirebaseSyncService $syncService)
    {
        $cedula = preg_replace('/[^0-9]/', '', $request->cedula);
        
        if (strlen($cedula) < 9) {
            return response()->json(['exists' => false]);
        }

        $result = $syncService->checkDocumentExistence('afiliados', $cedula);
        
        return response()->json($result);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('afiliados.index', $this->processIndex($request));
    }

    public function indexCmd(Request $request)
    {
        if (auth()->user()->isGestora()) {
            abort(403, 'No tienes permisos para acceder al módulo de Afiliados CMD.');
        }
        return view('afiliados.index', $this->processIndex($request, 'CMD'));
    }

    public function indexOtros(Request $request)
    {
        return view('afiliados.index', $this->processIndex($request, 'Otros'));
    }

    public function indexSalidaInmediata(Request $request)
    {
        $data = $this->processIndex($request, 'SalidaInmediata');
        
        if ($request->ajax()) {
            return view('afiliados.partials.salida_inmediata_table', $data)->render();
        }

        return view('afiliados.salida_inmediata', $data);
    }

    protected function processIndex(Request $request, $segment = null)
    {
        $query = \App\Models\Afiliado::with(['corte', 'responsable', 'estado', 'empresaModel', 'evidenciasAfiliado']);

        if ($segment === 'CMD') {
            $query->ars()->whereNotNull('responsable_id');
        } elseif ($segment === 'Otros') {
            $query->noArs()->whereNotNull('responsable_id');
        } elseif ($segment === 'SalidaInmediata') {
            $query->whereHas('empresaModel', function($q) {
                $q->where('es_verificada', true);
            })->whereNull('fecha_entrega_safesure');
        } else {
            // Módulo de Asignaciones: Solo lo que no tiene responsable
            $query->whereNull('responsable_id');
        }

        // Filtros existentes
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre_completo', 'like', '%' . $request->search . '%')
                  ->orWhere('cedula', 'like', '%' . $request->search . '%')
                  ->orWhere('contrato', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('corte_id')) {
            $query->where('corte_id', $request->corte_id);
        }
        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }

        // Filtros de Ubicación Normalizada (Herencia Afiliado -> Empresa)
        if ($request->filled('provincia_id')) {
            $query->where(function($q) use ($request) {
                $q->where('provincia_id', $request->provincia_id)
                  ->orWhereHas('empresaModel', function($qe) use ($request) {
                      $qe->where('provincia_id', $request->provincia_id);
                  });
            });
        }
        if ($request->filled('municipio_id')) {
            $query->where(function($q) use ($request) {
                $q->where('municipio_id', $request->municipio_id)
                  ->orWhereHas('empresaModel', function($qe) use ($request) {
                      $qe->where('municipio_id', $request->municipio_id);
                  });
            });
        }

        if ($request->filled('rnc_empresa')) {
            $query->where(function($q) use ($request) {
                $q->where('rnc_empresa', 'like', '%' . $request->rnc_empresa . '%')
                  ->orWhereHas('empresaModel', function($qe) use ($request) {
                      $qe->where('rnc', 'like', '%' . $request->rnc_empresa . '%');
                  });
            });
        }
        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }
        if ($request->filled('sexo')) {
            $query->where('sexo', $request->sexo);
        }
        if ($request->filled('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }
        if ($request->filled('reasignado')) {
            $query->where('reasignado', $request->reasignado);
        }
        if ($request->get('company_status') === 'none') {
            $query->whereNull('empresa_id');
        } elseif ($request->get('company_status') === 'assigned') {
            $query->whereNotNull('empresa_id');
        }
        if ($request->get('asignacion') === 'pendiente') {
            $query->whereNull('responsable_id');
        }

        // Ordenamiento
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        // Mapeo seguro de columnas para evitar inyección SQL
        $allowedSorts = [
            'nombre'      => 'nombre_completo',
            'cedula'      => 'cedula',
            'contrato'    => 'contrato',
            'empresa'     => 'empresa',
            'entrega'     => 'fecha_entrega_safesure',
            'creado'      => 'created_at',
            'estado'      => 'estado_id',
            'responsable' => 'responsable_id'
        ];

        $orderCol = $allowedSorts[$sort] ?? 'created_at';
        $query->orderBy($orderCol, $direction);

        $afiliados = $query->paginate(30)->withQueryString();

        // Métricas por Período para la vista (Segmentadas)
        $statsPorPeriodo = \App\Models\Corte::withCount([
            'afiliados as total' => function($q) use ($segment) {
                if ($segment === 'CMD') {
                    $q->ars()->whereNotNull('responsable_id');
                } elseif ($segment === 'Otros') {
                    $q->noArs()->whereNotNull('responsable_id');
                } else {
                    $q->whereNull('responsable_id');
                }
            },
            'afiliados as completados' => function($q) use ($segment) {
                if ($segment === 'CMD') {
                    $q->ars()->whereNotNull('responsable_id');
                } elseif ($segment === 'Otros') {
                    $q->noArs()->whereNotNull('responsable_id');
                } else {
                    $q->whereNull('responsable_id');
                }
                $q->whereHas('estado', function($e) { $e->where('nombre', 'Completado'); });
            }
        ])->get()->map(function($corte) {
            $corte->pendiente = $corte->total - $corte->completados;
            $corte->porcentaje = $corte->total > 0 ? round(($corte->completados / $corte->total) * 100) : 0;
            return $corte;
        })->filter(fn($c) => $c->total > 0);

        return compact('afiliados', 'segment', 'statsPorPeriodo');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $cortes = \App\Models\Corte::all();
        $estados = \App\Models\Estado::all();
        
        if (auth()->user()->isGestora()) {
            $responsables = \App\Models\Responsable::where('id', auth()->user()->responsable_id)->get();
        } else {
            $responsables = \App\Models\Responsable::all();
        }
        
        $empresas = \App\Models\Empresa::orderBy('nombre')->get();
        $provincias = \App\Models\Provincia::orderBy('nombre')->get();
        $municipios = collect();
        $segment = $request->get('segment');

        return view('afiliados.create', compact('cortes', 'estados', 'responsables', 'empresas', 'segment', 'provincias', 'municipios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAfiliadoRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            if ($request->filled('provincia_id')) {
                $validated['provincia'] = \App\Models\Provincia::find($request->provincia_id)?->nombre;
            }
            if ($request->filled('municipio_id')) {
                $validated['municipio'] = \App\Models\Municipio::find($request->municipio_id)?->nombre;
            }

            if ($request->filled('empresa_id')) {
                $empresa = \App\Models\Empresa::find($request->empresa_id);
                $validated['empresa'] = $empresa->nombre;
                $validated['rnc_empresa'] = $empresa->rnc;
            }

            // Create affiliate
            $afiliado = \App\Models\Afiliado::create($validated);

            // Assign initial status (audit / record history)
            $this->afiliadoService->updateStatus(
                $afiliado, 
                $request->estado_id, 
                'Registro creado manualmente con estado inicial: ' . (\App\Models\Estado::find($request->estado_id)?->nombre ?? 'N/A'),
                auth()->id()
            );

            DB::commit();

            // Validate that gestora users don't create CMD records
            if (auth()->user()->isGestora() && $request->segment === 'CMD') {
                throw new Exception("No tienes autorización para crear registros en el segmento CMD.");
            }

            if ($request->segment === 'CMD') {
                return redirect()->route('afiliados.cmd')->with('success', 'Afiliado CMD creado exitosamente.');
            } elseif ($request->segment === 'Otros') {
                return redirect()->route('afiliados.otros')->with('success', 'Afiliado de otra empresa creado exitosamente.');
            }

            return redirect()->route('afiliados.index')->with('success', 'Afiliado creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = $e->getMessage();
            if ($e->getCode() == 23000 || str_contains($msg, 'Duplicate entry')) {
                $msg = "Error: El afiliado ya existe en este corte (Cédula duplicada para este periodo).";
            }
            return back()->withInput()->with('error', $msg);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\Afiliado $afiliado)
    {
        // Real-time pull from Firebase to identify CMD changes
        if ($remoteData = $this->firebaseSync->pull('afiliados', $afiliado->cedula)) {
            $this->firebaseSync->syncLocalModel($afiliado, $remoteData);
        }
        
        $afiliado->load(['corte', 'responsable', 'estado', 'empresaModel', 'evidenciasAfiliado', 'historialEstados.user', 'notas.user']);
        return view('afiliados.show', compact('afiliado'));
    }

    public function edit(\App\Models\Afiliado $afiliado)
    {
        // Real-time pull before editing to avoid overwriting newer CMD data
        if ($remoteData = $this->firebaseSync->pull('afiliados', $afiliado->cedula)) {
            $this->firebaseSync->syncLocalModel($afiliado, $remoteData);
        }
        
        $cortes = \App\Models\Corte::all();
        $estados = \App\Models\Estado::all();
        
        if (auth()->user()->isGestora()) {
            $responsables = \App\Models\Responsable::where('id', auth()->user()->responsable_id)->get();
        } else {
            $responsables = \App\Models\Responsable::all();
        }
        
        $empresas = \App\Models\Empresa::orderBy('nombre')->get();
        $provincias = \App\Models\Provincia::orderBy('nombre')->get();

        // Lógica de Homologación de Ubicación (Estructura Firebase)
        // Si no tiene IDs pero sí nombres en texto, intentamos empatar para que el formulario se precargue bien
        if (!$afiliado->provincia_id && $afiliado->provincia) {
            $afiliado->provincia_id = \App\Models\Provincia::where('nombre', 'like', "%{$afiliado->provincia}%")->first()?->id;
        }
        if ($afiliado->provincia_id && !$afiliado->municipio_id && $afiliado->municipio) {
            $afiliado->municipio_id = \App\Models\Municipio::where('provincia_id', $afiliado->provincia_id)
                ->where('nombre', 'like', "%{$afiliado->municipio}%")
                ->first()?->id;
        }

        $municipios = $afiliado->provincia_id ? \App\Models\Municipio::where('provincia_id', $afiliado->provincia_id)->orderBy('nombre')->get() : collect();

        return view('afiliados.edit', compact('afiliado', 'cortes', 'estados', 'responsables', 'empresas', 'provincias', 'municipios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAfiliadoRequest $request, \App\Models\Afiliado $afiliado)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Si se seleccionó una empresa del módulo, actualizamos también los campos legacy por compatibilidad
            // Sincronizar nombres descriptivos de ubicación para retrocompatibilidad
            if ($request->filled('provincia_id')) {
                $validated['provincia'] = \App\Models\Provincia::find($request->provincia_id)?->nombre;
            } else {
                $validated['provincia'] = null;
            }

            if ($request->filled('municipio_id')) {
                $validated['municipio'] = \App\Models\Municipio::find($request->municipio_id)?->nombre;
            } else {
                $validated['municipio'] = null;
            }

        if ($request->filled('empresa_id')) {
                $empresa = \App\Models\Empresa::find($request->empresa_id);
                $validated['empresa'] = $empresa->nombre;
                $validated['rnc_empresa'] = $empresa->rnc;
            }

            // Handle metadata update
            $afiliado->update(collect($validated)->except('estado_id')->toArray());

            // Handle status change via Service Layer (for business rules)
            $this->afiliadoService->updateStatus(
                $afiliado, 
                $request->estado_id, 
                'Cambio de estado desde edición manual.',
                auth()->id()
            );

            DB::commit();
            return back()->with('success', 'Afiliado actualizado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function reassign(Request $request, Afiliado $afiliado)
    {
        $request->validate([
            'responsable_id' => 'required|exists:responsables,id',
        ]);

        try {
            DB::beginTransaction();
            if ($afiliado->estado?->es_final || strtolower($afiliado->estado?->nombre) === 'completado') {
                throw new Exception("Regla 5.3: No se puede reasignar un expediente COMPLETADO sin reapertura.");
            }

            $oldResponsable = $afiliado->responsable?->nombre ?? 'Sin Asignar';
            $afiliado->responsable_id = $request->responsable_id;
            $afiliado->reasignado = true; // Flag for audit
            $afiliado->save();
            $newResponsable = $afiliado->fresh()->responsable?->nombre ?? 'Sin Asignar';

            \App\Models\HistorialEstado::create([
                'afiliado_id' => $afiliado->id,
                'estado_anterior_id' => $afiliado->estado_id,
                'estado_nuevo_id' => $afiliado->estado_id,
                'user_id' => auth()->id() ?? 1,
                'observacion' => "Reasignación de Responsable: {$oldResponsable} -> {$newResponsable}"
            ]);

            DB::commit();
            return back()->with('success', "Responsable cambiado a {$newResponsable} exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'selected' => 'required|array',
            'responsable_id' => 'required|exists:responsables,id',
            'segment' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            $afiliados = \App\Models\Afiliado::whereIn('id', $request->selected)->get();
            $segment = $request->input('segment');
            
            foreach($afiliados as $afiliado) {
                /** @var Afiliado $afiliado */
                // Rule 5.3: Immutability check
                if (strtolower($afiliado->estado?->nombre) === 'completado') {
                    throw new Exception("Regla 5.3: Algunos registros están COMPLETADOS y no pueden ser reasignados.");
                }

                $afiliado->responsable_id = $request->responsable_id;
                $afiliado->reasignado = true; // Flag for audit
                
                // La segmentación ahora depende del Responsable asignado, no forzamos la empresa.
                $afiliado->save();

                \App\Models\HistorialEstado::create([
                    'afiliado_id' => $afiliado->id,
                    'estado_anterior_id' => $afiliado->estado_id,
                    'estado_nuevo_id' => $afiliado->estado_id,
                    'user_id' => auth()->id() ?? 1,
                    'observacion' => 'Asignado a responsable masivamente.'
                ]);
            }
            DB::commit();
            return back()->with('success', count($request->selected) . ' afiliados asignados exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function bulkCompany(Request $request)
    {
        $request->validate([
            'selected' => 'required|array',
            'empresa_id' => 'required|exists:empresas,id',
            'segment' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            $afiliados = \App\Models\Afiliado::whereIn('id', $request->selected)->get();
            $empresa = \App\Models\Empresa::findOrFail($request->empresa_id);
            
            foreach($afiliados as $afiliado) {
                /** @var \App\Models\Afiliado $afiliado */
                $oldEmpresa = $afiliado->empresa ?? 'Sin Empresa';
                $afiliado->empresa_id = $empresa->id;
                $afiliado->empresa = $empresa->nombre;
                $afiliado->rnc_empresa = $empresa->rnc;
                $afiliado->save();

                \App\Models\HistorialEstado::create([
                    'afiliado_id' => $afiliado->id,
                    'estado_anterior_id' => $afiliado->estado_id,
                    'estado_nuevo_id' => $afiliado->estado_id,
                    'user_id' => auth()->id() ?? 1,
                    'observacion' => "Asignación Masiva de Empresa: {$oldEmpresa} -> {$empresa->nombre}"
                ]);
            }

            DB::commit();
            
            return back()->with('success', count($request->selected) . ' empresas actualizadas masivamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error en asignación masiva de empresa: ' . $e->getMessage());
        }
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'selected' => 'required|array',
            'estado_id' => 'required|exists:estados,id',
            'motivo_rapido' => 'nullable|string',
            'observacion' => 'nullable|string',
            'segment' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            $afiliados = \App\Models\Afiliado::whereIn('id', $request->selected)->get();
            
            // Determinar la observación final (prioridad al motivo rápido)
            $observacionFinal = $request->motivo_rapido ?: ($request->observacion ?? 'Cambio de estado masivo.');

            foreach($afiliados as $afiliado) {
                /** @var Afiliado $afiliado */
                $this->afiliadoService->updateStatus(
                    $afiliado,
                    $request->estado_id,
                    $observacionFinal,
                    auth()->id()
                );
            }
            DB::commit();
            return back()->with('success', count($request->selected) . ' afiliados actualizados exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Afiliado $afiliado)
    {
        $request->validate([
            'estado_id' => 'required|exists:estados,id',
            'motivo_rapido' => 'nullable|string',
            'observacion' => 'nullable|string|max:1000'
        ]);

        try {
            $observacionFinal = $request->motivo_rapido ?: ($request->observacion ?? 'Estado actualizado individualmente.');
            $this->afiliadoService->updateStatus($afiliado, $request->estado_id, $observacionFinal, auth()->id());
            return back()->with('success', 'Estado del afiliado actualizado correctamente.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function uploadEvidencia(Request $request, Afiliado $afiliado)
    {
        $request->validate([
            'tipo_documento' => 'required|in:acuse_recibo,formulario_firmado',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        try {
            $this->evidenciaService->upload(
                $afiliado, 
                $request->tipo_documento, 
                $request->file('file'), 
                auth()->id() ?? 1,
                $request->observaciones
            );
            return back()->with('success', 'Evidencia subida y procesada correctamente.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function searchAjax(Request $request)
    {
        $search = $request->get('q');
        
        if (strlen($search) < 3) {
            return response()->json([]);
        }

        $afiliados = \App\Models\Afiliado::where(function($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%")
                  ->orWhere('poliza', 'like', "%{$search}%")
                  ->orWhere('rnc_empresa', 'like', "%{$search}%")
                  ->orWhere('empresa', 'like', "%{$search}%");
            })
            ->with(['estado', 'responsable'])
            ->limit(8)
            ->get()
            ->filter(function($af) {
                // Si el usuario es gestora, solo puede ver sus propios afiliados (ResponsableScope ya lo hace en la query, 
                // pero por precaución validamos el segmento CMD)
                if (auth()->user()->isGestora()) {
                    return !str_contains(strtoupper($af->responsable?->nombre ?? ''), 'CMD');
                }
                return true;
            })
            ->map(function($af) {
                return [
                    'id' => $af->id,
                    'nombre' => $af->nombre_completo,
                    'cedula' => $af->cedula,
                    'poliza' => $af->poliza ?? 'N/A',
                    'estado' => $af->estado->nombre ?? 'Sin Estado',
                    'responsable' => $af->responsable->nombre ?? 'Sin Asignar',
                    'url' => route('afiliados.show', $af)
                ];
            })
            ->values();

        return response()->json($afiliados);
    }
    public function export(Request $request)
    {
        return Excel::download(new AfiliadosExport($request->all()), 'afiliados_syscarnet_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function sanitizeAddresses()
    {
        try {
            DB::beginTransaction();

            $afiliados = \App\Models\Afiliado::whereNotNull('direccion')->get();
            $count = 0;

            foreach ($afiliados as $afiliado) {
                /** @var Afiliado $afiliado */
                $original = $afiliado->direccion;
                $afiliado->normalizeAddress();
                
                if ($original !== $afiliado->direccion) {
                    $afiliado->save();
                    $count++;
                }
            }

            DB::commit();
            return back()->with('success', "Se han normalizado {$count} direcciones exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
