<?php

namespace App\Mail;

use App\Models\LibroReclamacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReclamacionRegistradaMailable extends Mailable
{
    use Queueable, SerializesModels;

    public LibroReclamacion $reclamacion;
    public bool $esParaAdmin;

    public function __construct(LibroReclamacion $reclamacion, bool $esParaAdmin = false)
    {
        $this->reclamacion = $reclamacion;
        $this->esParaAdmin = $esParaAdmin;
    }

    public function envelope(): Envelope
    {
        $siteNombre = $this->reclamacion->site?->nombre ?? config('app.name', 'Tienda Virtual');
        $tipoLabel = strtoupper($this->reclamacion->tipo);

        if ($this->esParaAdmin) {
            $asunto = "🚨 [NUEVO {$tipoLabel}] #{$this->reclamacion->codigo} - {$this->reclamacion->nombre_completo}";
        } else {
            $asunto = "Constancia de Registro - Libro de Reclamaciones #{$this->reclamacion->codigo} | {$siteNombre}";
        }

        return new Envelope(
            subject: $asunto,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reclamacion_registrada',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

