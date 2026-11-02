<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Cotización Web</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; line-height: 1.5;">

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 30px 12px;">
        <tr>
            <td align="center">
                <!-- CONTENEDOR PRINCIPAL (Max 640px) -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 640px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                    
                    <!-- LÍNEA SUPERIOR DECORATIVA -->
                    <tr>
                        <td style="height: 4px; background-color: #0f172a;"></td>
                    </tr>

                    <!-- 1. ENCABEZADO INSTITUCIONAL -->
                    <tr>
                        <td style="padding: 26px 32px 20px 32px; border-bottom: 1px solid #edf2f7;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td valign="middle" align="left" style="width: 55%;">
                                        @php
                                            $logoSrc = null;
                                            if (!empty($sitio_logo_path) && file_exists($sitio_logo_path)) {
                                                try {
                                                    $logoSrc = $message->embed($sitio_logo_path);
                                                } catch (\Throwable $e) {
                                                    $logoSrc = $sitio_logo;
                                                }
                                            } elseif (!empty($sitio_logo)) {
                                                $logoSrc = $sitio_logo;
                                            }
                                        @endphp

                                        @if(!empty($logoSrc))
                                            <img src="{{ $logoSrc }}" alt="{{ $sitio_nombre }}" style="max-height: 48px; max-width: 220px; object-fit: contain; display: block;" />
                                        @else
                                            <h1 style="margin: 0; font-size: 20px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; text-transform: uppercase;">
                                                {{ $sitio_nombre }}
                                            </h1>
                                        @endif
                                        <span style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.8px;">
                                            Plataforma de Atención Comercial
                                        </span>
                                    </td>
                                    <td valign="middle" align="right" style="width: 45%;">
                                        <div style="display: inline-block; text-align: right;">
                                            <span style="display: block; font-size: 10px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 1px;">
                                                REGISTRO DE COTIZACIÓN
                                            </span>
                                            <span style="display: block; font-size: 12px; font-family: monospace; font-weight: 700; color: #0f172a; margin-top: 2px;">
                                                N° {{ $folio }}
                                            </span>
                                            <span style="display: block; font-size: 11px; color: #94a3b8; margin-top: 2px;">
                                                {{ $fecha_envio }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 2. ÍTEM / PRODUCTO REQUERIDO (ESTILO PROFORMA) -->
                    @if(!empty($producto))
                    <tr>
                        <td style="padding: 22px 32px 10px 32px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #0f172a; border-radius: 6px; padding: 12px 18px;">
                                <tr>
                                    <td>
                                        <span style="display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.8px;">
                                            ÍTEM / PRODUCTO SOLICITADO
                                        </span>
                                        <span style="display: block; font-size: 15px; font-weight: 800; color: #0f172a; margin-top: 3px;">
                                            {{ $producto }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <!-- 3. CUADRO COMPARATIVO: DATOS DEL SOLICITANTE Y VÍAS DE CONTACTO (2 COLUMNAS) -->
                    <tr>
                        <td style="padding: 16px 32px 10px 32px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <!-- COLUMNA IZQUIERDA: INFORMACIÓN DEL SOLICITANTE -->
                                    <td valign="top" width="48%" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px 16px;">
                                        <span style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569; letter-spacing: 0.5px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px; margin-bottom: 10px;">
                                            Información del Solicitante
                                        </span>
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 12px;">
                                            <tr>
                                                <td style="color: #64748b; padding: 3px 0; font-weight: 600;">Nombre / Razón:</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #0f172a; font-weight: 700; padding-bottom: 6px;">{{ $nombre }}</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; padding: 3px 0; font-weight: 600;">Empresa / Entidad:</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #0f172a; font-weight: 600; padding-bottom: 6px;">{{ $empresa ?: 'No especificada' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; padding: 3px 0; font-weight: 600;">Sector Comercial:</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #0f172a; font-weight: 600;">{{ $sector ?: 'General' }}</td>
                                            </tr>
                                        </table>
                                    </td>

                                    <td width="4%"></td>

                                    <!-- COLUMNA DERECHA: CANALES DE CONTACTO DIRECTO -->
                                    <td valign="top" width="48%" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px 16px;">
                                        <span style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569; letter-spacing: 0.5px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px; margin-bottom: 10px;">
                                            Canales de Contacto
                                        </span>
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 12px;">
                                            <tr>
                                                <td style="color: #64748b; padding: 3px 0; font-weight: 600;">Teléfono / WhatsApp:</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 6px;">
                                                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $telefono) }}" style="color: #0f172a; font-weight: 700; text-decoration: none;">
                                                        {{ $telefono }}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; padding: 3px 0; font-weight: 600;">Correo Electrónico:</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 6px;">
                                                    @if(!empty($email))
                                                        <a href="mailto:{{ $email }}" style="color: #0f172a; font-weight: 600; text-decoration: underline;">
                                                            {{ $email }}
                                                        </a>
                                                    @else
                                                        <span style="color: #94a3b8;">No especificado</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; padding: 3px 0; font-weight: 600;">Canal de Origen:</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #0f172a; font-weight: 600;">Formulario Web Oficial</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 4. REQUERIMIENTO / MENSAJE DEL CLIENTE -->
                    <tr>
                        <td style="padding: 12px 32px 20px 32px;">
                            <span style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569; letter-spacing: 0.5px; margin-bottom: 8px;">
                                Requerimiento / Especificaciones del Cliente:
                            </span>
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px 20px; font-size: 13px; color: #334155; line-height: 1.6; white-space: pre-line;">{{ $mensaje }}</div>
                        </td>
                    </tr>

                    <!-- 5. BARRA DE ACCIONES EJECUTIVAS (DOBLE BOTÓN) -->
                    @php
                        $cleanPhone = preg_replace('/\D/', '', $telefono ?? '');
                        if ($cleanPhone && strlen($cleanPhone) === 9) {
                            $cleanPhone = '51' . $cleanPhone;
                        }
                        $waText = "Estimado(a) {$nombre}, le saluda el área comercial de {$sitio_nombre}. Recibimos su solicitud de cotización por {$producto}.";
                        $waUrl = $cleanPhone ? "https://wa.me/{$cleanPhone}?text=" . urlencode($waText) : null;
                    @endphp

                    <tr>
                        <td style="padding: 0 32px 28px 32px; text-align: center;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto;">
                                <tr>
                                    @if($waUrl)
                                    <td style="padding: 0 6px;">
                                        <a href="{{ $waUrl }}" target="_blank" style="display: inline-block; background-color: #15803d; color: #ffffff; text-decoration: none; font-size: 13px; font-weight: 700; padding: 11px 22px; border-radius: 6px;">
                                            Contactar vía WhatsApp
                                        </a>
                                    </td>
                                    @endif

                                    @if(!empty($email))
                                    <td style="padding: 0 6px;">
                                        <a href="mailto:{{ $email }}?subject={{ urlencode('Respuesta a Cotización: ' . ($producto ?: 'Requerimiento') . ' - ' . $sitio_nombre) }}" style="display: inline-block; background-color: #ffffff; color: #0f172a; text-decoration: none; font-size: 13px; font-weight: 700; padding: 10px 20px; border-radius: 6px; border: 1px solid #cbd5e1;">
                                            Responder por Correo
                                        </a>
                                    </td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 6. PIE DE PÁGINA CORPORATIVO -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 22px 32px; color: #64748b; font-size: 11px; line-height: 1.5;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="left">
                                        <strong style="color: #334155;">{{ $sitio_nombre }}</strong>
                                        @if(!empty($sitio_direccion))
                                            <span style="display: block; color: #94a3b8; margin-top: 2px;">{{ $sitio_direccion }}</span>
                                        @endif
                                    </td>
                                    <td align="right" style="vertical-align: top; color: #94a3b8;">
                                        Documento generado automáticamente
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 12px; border-top: 1px solid #edf2f7; margin-top: 10px; font-size: 10px; color: #94a3b8; text-align: justify; line-height: 1.4;">
                                        AVISO DE CONFIDENCIALIDAD: Este mensaje contiene información comercial confidencial destinada exclusivamente para la atención de ventas de {{ $sitio_nombre }}. Si usted ha recibido este correo por error, por favor notifíquelo y elimine este mensaje.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
