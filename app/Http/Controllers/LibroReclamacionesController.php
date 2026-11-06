<?php

namespace App\Http\Controllers;

use App\Mail\ReclamacionRegistradaMailable;
use App\Models\LibroReclamacion;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LibroReclamacionesController extends Controller
{
    /**
     * Registra un reclamo o queja en el Libro de Reclamaciones Virtual
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'tipo' => 'required|in:reclamo,queja',
            'tipo_documento' => 'required|in:DNI,CE,RUC,Pasaporte',
            'numero_documento' => 'required|string|max:30',
            'nombres' => 'required|string|max:150',
            'apellidos' => 'nullable|string|max:150',
            'razon_social' => 'nullable|string|max:200',
            'email' => 'required|email|max:150',
            'telefono' => 'required|string|max:50',
            'departamento' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
            'distrito' => 'nullable|string|max:100',
            'direccion' => 'required|string|max:255',
            'es_menor_edad' => 'nullable|boolean',
            'apoderado_nombres' => 'nullable|string|max:200',
            'apoderado_tipo_documento' => 'nullable|string|max:20',
            'apoderado_numero_documento' => 'nullable|string|max:30',
            'apoderado_email' => 'nullable|email|max:150',
            'apoderado_telefono' => 'nullable|string|max:50',
            'tipo_bien' => 'required|in:producto,servicio',
            'monto_reclamado' => 'nullable|numeric|min:0',
            'moneda' => 'nullable|string|max:10',
            'numero_pedido' => 'nullable|string|max:100',
            'descripcion_bien' => 'required|string|max:1500',
            'detalle' => 'required|string|max:4000',
            'pedido' => 'required|string|max:2000',
        ], [
            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'numero_documento.required' => 'El número de documento es obligatorio.',
            'nombres.required' => 'El nombre del consumidor o razón social es obligatorio.',
            'email.required' => 'El correo electrónico es indispensable para remitir la constancia.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'telefono.required' => 'El teléfono de contacto es obligatorio.',
            'direccion.required' => 'El domicilio es obligatorio conforme al Código de Protección al Consumidor.',
            'descripcion_bien.required' => 'Debe describir el producto o servicio materia del reclamo.',
            'detalle.required' => 'Debe detallar los hechos que motivan su reclamación o queja.',
            'pedido.required' => 'Debe ingresar la pretensión o solución que solicita.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor verifique los campos requeridos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $siteId = (int) $validated['site_id'];
        $tipo = $validated['tipo'];

        // Generar correlativo oficial único (REC-2026-0001 o QUE-2026-0001)
        $codigo = LibroReclamacion::generarSiguienteCodigo($siteId, $tipo);

        // Calcular fecha límite de respuesta legal (15 días hábiles según INDECOPI)
        $fechaLimite = LibroReclamacion::calcularFechaLimite(15);

        $reclamacion = new LibroReclamacion();
        $reclamacion->fill($validated);
        $reclamacion->codigo = $codigo;
        $reclamacion->estado = 'pendiente';
        $reclamacion->fecha_limite = $fechaLimite;
        $reclamacion->ip_origen = $request->ip();
        $reclamacion->user_agent = substr($request->userAgent() ?? '', 0, 500);
        $reclamacion->save();

        // Cargar relación con el sitio para los correos
        $reclamacion->load('site');

        // Determinar correo receptor para administración: primero del sitio, luego .env
        $siteEmail = $reclamacion->site?->estilos['libro_reclamaciones']['email_receptor'] ?? null;
        $adminEmail = (!empty($siteEmail) && filter_var($siteEmail, FILTER_VALIDATE_EMAIL))
            ? $siteEmail
            : (env('MAIL_RECLAMACIONES_RECEIVER')
                ?: env('MAIL_RECLAMACIONES_DESTINATARIO')
                ?: config('mail.from.address'));

        // Envío de correos asíncrono o seguro con try-catch
        try {
            // 1. Al correo configurado en el .env
            if (! empty($adminEmail)) {
                Mail::to($adminEmail)->send(new ReclamacionRegistradaMailable($reclamacion, true));
            }

            // 2. Copia de constancia al consumidor
            if (! empty($reclamacion->email)) {
                Mail::to($reclamacion->email)->send(new ReclamacionRegistradaMailable($reclamacion, false));
            }
        } catch (\Throwable $e) {
            Log::error('Error al enviar correos del Libro de Reclamaciones: ' . $e->getMessage(), [
                'reclamacion_id' => $reclamacion->id,
                'codigo' => $reclamacion->codigo,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Su reclamación ha sido registrada correctamente.',
            'data' => [
                'codigo' => $reclamacion->codigo,
                'tipo' => $reclamacion->tipo,
                'fecha_registro' => $reclamacion->created_at->format('d/m/Y H:i:s'),
                'fecha_limite' => $reclamacion->fecha_limite ? $reclamacion->fecha_limite->format('d/m/Y') : null,
                'email_notificado' => $reclamacion->email,
            ],
        ]);
    }
}

