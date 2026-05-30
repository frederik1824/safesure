<?php

namespace App\Exports;

use App\Models\Traspaso;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class TraspasosExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $traspasos;

    public function __construct($traspasos)
    {
        $this->traspasos = $traspasos;
    }

    public function collection()
    {
        return $this->traspasos;
    }

    public function headings(): array
    {
        return [
            'ID CMD',
            'Nombre Afiliado',
            'Cédula',
            'Sexo',
            'Fecha Nacimiento',
            'Edad',
            'Agente',
            'Estado',
            'Dependientes',
            'Fecha Solicitud',
            'Fecha Efectivo',
            'Periodo',
            'Estado Unipago',
            'Motivo Rechazo',
            'Fecha Rechazo'
        ];
    }

    public function map($tr): array
    {
        $edad = $tr->fecha_nacimiento ? Carbon::parse($tr->fecha_nacimiento)->age : 'N/D';
        $sexoLargo = $tr->sexo === 'M' ? 'Masculino' : ($tr->sexo === 'F' ? 'Femenino' : $tr->sexo);

        return [
            $tr->firebase_document_id,
            $tr->nombre_afiliado,
            $tr->cedula_afiliado,
            $sexoLargo,
            $tr->fecha_nacimiento ? $tr->fecha_nacimiento->format('d/m/Y') : '',
            $edad,
            $tr->agente,
            $tr->estado,
            $tr->cantidad_dependientes,
            $tr->fecha_solicitud ? $tr->fecha_solicitud->format('d/m/Y') : '',
            $tr->fecha_efectivo ? $tr->fecha_efectivo->format('d/m/Y') : '',
            $tr->periodo,
            $tr->status_unipago,
            $tr->motivo_rechazo,
            $tr->fecha_rechazo ? $tr->fecha_rechazo->format('d/m/Y') : ''
        ];
    }
}
