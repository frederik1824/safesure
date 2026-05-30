<?php

namespace App\Exports;

use App\Models\Afiliado;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupervisionExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Afiliado::with(['corte', 'estado', 'responsable', 'empresaModel']);

        if (!empty($this->filters['fecha_desde'])) {
            $query->whereDate('created_at', '>=', $this->filters['fecha_desde']);
        }

        if (!empty($this->filters['fecha_hasta'])) {
            $query->whereDate('created_at', '<=', $this->filters['fecha_hasta']);
        }

        if (!empty($this->filters['corte_id'])) {
            $query->where('corte_id', $this->filters['corte_id']);
        }

        if (!empty($this->filters['responsable_id'])) {
            $query->where('responsable_id', $this->filters['responsable_id']);
        }

        if (!empty($this->filters['empresa_id'])) {
            $query->where('empresa_id', $this->filters['empresa_id']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre Completo',
            'Cédula',
            'Contrato',
            'Empresa',
            'RNC Empresa',
            'Corte',
            'Estado',
            'Responsable',
            'Fecha Ingreso',
            'Fecha Entrega Prov',
            'SLA Status',
            'Costo Entrega',
            'Liquidado',
            'Número de Solicitud',
            'Cantidad de Dependientes',
            'Sexo',
            'Fecha de Nacimiento'
        ];
    }

    public function map($a): array
    {
        return [
            $a->id,
            $a->nombre_completo,
            $a->cedula,
            $a->contrato,
            $a->empresaModel?->nombre ?? $a->empresa,
            $a->rnc_empresa,
            $a->corte?->nombre ?? 'N/A',
            $a->estado?->nombre ?? 'N/A',
            $a->responsable?->nombre ?? 'N/A',
            $a->created_at->format('Y-m-d'),
            $a->fecha_entrega_proveedor?->format('Y-m-d') ?? '',
            $a->sla_status,
            $a->costo_entrega,
            $a->liquidado ? 'SI' : 'NO',
            $a->numero_solicitud,
            $a->cantidad_dependientes,
            $a->sexo === 'M' ? 'Masculino' : ($a->sexo === 'F' ? 'Femenino' : ($a->sexo ?: 'N/D')),
            $a->fecha_nacimiento ? $a->fecha_nacimiento->format('Y-m-d') : ''
        ];
    }
}
