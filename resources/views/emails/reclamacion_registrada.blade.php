<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro de Reclamaciones - {{ $reclamacion->codigo }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            margin: 0;
            padding: 24px 12px;
            line-height: 1.5;
        }
        .container {
            max-width: 680px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #0f172a;
            color: #ffffff;
            padding: 24px 32px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 6px;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 13px;
            color: #cbd5e1;
        }
        .badge-bar {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }
        .codigo-badge {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            padding: 4px 12px;
            border-radius: 9999px;
            font-weight: 700;
            font-family: monospace;
            font-size: 13px;
        }
        .tipo-tag {
            font-weight: 700;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        .tipo-reclamo {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .tipo-queja {
            background-color: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .content {
            padding: 28px 32px;
        }
        .alert-admin {
            background-color: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #1e40af;
        }
        .section-box {
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            background: #ffffff;
        }
        .section-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin: 0 0 12px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
        }
        .data-grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .data-grid td {
            padding: 6px 4px;
            vertical-align: top;
        }
        .data-label {
            width: 38%;
            color: #64748b;
            font-weight: 600;
        }
        .data-value {
            color: #0f172a;
            font-weight: 500;
        }
        .text-block {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 13px;
            color: #1e293b;
            white-space: pre-line;
            line-height: 1.6;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 32px;
            font-size: 12px;
            color: #64748b;
            text-align: center;
            line-height: 1.5;
        }
        .footer strong {
            color: #334155;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER OFICIAL -->
        <div class="header">
            <h1>LIBRO DE RECLAMACIONES VIRTUAL</h1>
            <p>Hoja de Reclamación Digital N° {{ $reclamacion->codigo }}</p>
            <p style="font-size: 11px; margin-top: 4px; opacity: 0.8;">
                {{ $reclamacion->site?->nombre ?? 'Nuestra Tienda' }}
            </p>
        </div>

        <!-- BARRA DE CÓDIGO Y ESTADO -->
        <div class="badge-bar">
            <div>
                <span>Código: </span>
                <span class="codigo-badge">{{ $reclamacion->codigo }}</span>
            </div>
            <div>
                <span class="tipo-tag {{ $reclamacion->tipo === 'queja' ? 'tipo-queja' : 'tipo-reclamo' }}">
                    {{ strtoupper($reclamacion->tipo) }}
                </span>
            </div>
        </div>

        <div class="content">
            @if($esParaAdmin)
                <div class="alert-admin">
                    <strong>🚨 Notificación de Reclamación Recibida:</strong>
                    Se ha registrado un nuevo requerimiento en la tienda online. Conforme a ley (Ley N° 29571 - INDECOPI), la empresa dispone de un plazo máximo de <strong>15 días hábiles</strong> para emitir respuesta formal al consumidor (Fecha límite estimada: <strong>{{ $reclamacion->fecha_limite ? $reclamacion->fecha_limite->format('d/m/Y') : '15 días hábiles' }}</strong>).
                </div>
            @else
                <p style="font-size: 14px; color: #334155; margin-top: 0; margin-bottom: 20px;">
                    Estimado(a) <strong>{{ $reclamacion->nombre_completo }}</strong>, hemos registrado su Hoja de Reclamación con éxito. A continuación, le enviamos una copia con todos los datos consignados para su seguimiento.
                </p>
            @endif

            <!-- 1. DATOS DEL RECLAMANTE -->
            <div class="section-box">
                <div class="section-title">1. Identificación del Consumidor Reclamante</div>
                <table class="data-grid">
                    <tr>
                        <td class="data-label">Consumidor:</td>
                        <td class="data-value">{{ $reclamacion->nombre_completo }}</td>
                    </tr>
                    <tr>
                        <td class="data-label">Documento:</td>
                        <td class="data-value">{{ $reclamacion->tipo_documento }}: {{ $reclamacion->numero_documento }}</td>
                    </tr>
                    @if($reclamacion->tipo_documento === 'RUC' && $reclamacion->nombres)
                        <tr>
                            <td class="data-label">Contacto / Representante:</td>
                            <td class="data-value">{{ $reclamacion->nombres }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="data-label">Correo electrónico:</td>
                        <td class="data-value">
                            <a href="mailto:{{ $reclamacion->email }}" style="color: #2563eb; text-decoration: none;">
                                {{ $reclamacion->email }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="data-label">Teléfono / Celular:</td>
                        <td class="data-value">{{ $reclamacion->telefono }}</td>
                    </tr>
                    <tr>
                        <td class="data-label">Domicilio:</td>
                        <td class="data-value">
                            {{ $reclamacion->direccion }}
                            @if($reclamacion->distrito || $reclamacion->provincia || $reclamacion->departamento)
                                <br><span style="color: #64748b; font-size: 12px;">({{ implode(', ', array_filter([$reclamacion->distrito, $reclamacion->provincia, $reclamacion->departamento])) }})</span>
                            @endif
                        </td>
                    </tr>

                    @if($reclamacion->es_menor_edad)
                        <tr>
                            <td class="data-label" style="color: #b45309;">Padre / Madre / Apoderado:</td>
                            <td class="data-value" style="color: #b45309; font-weight: 600;">
                                {{ $reclamacion->apoderado_nombres }} 
                                @if($reclamacion->apoderado_numero_documento)
                                    ({{ $reclamacion->apoderado_tipo_documento ?? 'DOC' }}: {{ $reclamacion->apoderado_numero_documento }})
                                @endif
                            </td>
                        </tr>
                    @endif
                </table>
            </div>

            <!-- 2. IDENTIFICACIÓN DEL BIEN CONTRATADO -->
            <div class="section-box">
                <div class="section-title">2. Identificación del Bien Contratado</div>
                <table class="data-grid">
                    <tr>
                        <td class="data-label">Tipo de Bien:</td>
                        <td class="data-value">
                            <strong>{{ ucfirst($reclamacion->tipo_bien) }}</strong>
                        </td>
                    </tr>
                    @if($reclamacion->monto_reclamado)
                        <tr>
                            <td class="data-label">Monto Reclamado:</td>
                            <td class="data-value" style="font-weight: 700; color: #047857;">
                                {{ $reclamacion->moneda === 'USD' ? '$' : 'S/' }} {{ number_format($reclamacion->monto_reclamado, 2) }}
                            </td>
                        </tr>
                    @endif
                    @if($reclamacion->numero_pedido)
                        <tr>
                            <td class="data-label">N° Pedido / Comprobante:</td>
                            <td class="data-value">{{ $reclamacion->numero_pedido }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="data-label">Descripción del Bien:</td>
                        <td class="data-value">{{ $reclamacion->descripcion_bien }}</td>
                    </tr>
                </table>
            </div>

            <!-- 3. DETALLE DE LA RECLAMACIÓN -->
            <div class="section-box">
                <div class="section-title">3. Detalle de la Reclamación y Pedido del Consumidor</div>
                
                <p style="margin: 0 0 6px 0; font-size: 12px; font-weight: 700; color: #475569;">
                    Hechos que motivan la disconformidad ({{ ucfirst($reclamacion->tipo) }}):
                </p>
                <div class="text-block" style="margin-bottom: 16px;">{{ $reclamacion->detalle }}</div>

                <p style="margin: 0 0 6px 0; font-size: 12px; font-weight: 700; color: #475569;">
                    Pedido concreto del consumidor (Solución solicitada):
                </p>
                <div class="text-block">{{ $reclamacion->pedido }}</div>
            </div>

            <!-- FECHA DE REGISTRO Y PLAZO -->
            <div style="background: #f8fafc; border-radius: 8px; padding: 12px 16px; font-size: 12px; color: #475569; display: flex; justify-content: space-between; align-items: center;">
                <span><strong>Fecha de Registro:</strong> {{ $reclamacion->created_at ? $reclamacion->created_at->format('d/m/Y H:i:s') : date('d/m/Y H:i:s') }}</span>
                <span><strong>Plazo legal:</strong> 15 días hábiles</span>
            </div>
        </div>

        <!-- FOOTER LEGAL -->
        <div class="footer">
            <p style="margin: 0 0 6px 0;">
                Conforme al Código de Protección y Defensa del Consumidor (Ley N° 29571) - INDECOPI.
            </p>
            <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                * La formulación del reclamo no excluye el recurso a otros medios de solución de controversias ni es requisito previo para interponer una denuncia ante INDECOPI.
            </p>
        </div>
    </div>
</body>
</html>

