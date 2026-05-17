<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FirebaseAlertNotification extends Notification
{
    use Queueable;

    public $alertType;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($alertType, $message)
    {
        $this->alertType = $alertType;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // En un entorno de producción, aquí se puede agregar 'slack' o 'telegram'
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->error()
                    ->subject("ALERTA CRÍTICA NEXUS: {$this->alertType}")
                    ->greeting('Alerta del Sistema de Sincronización SafeSync Nexus')
                    ->line('Se ha detectado una anomalía crítica en el motor de sincronización:')
                    ->line("**Tipo de Alerta:** {$this->alertType}")
                    ->line("**Detalles:** {$this->message}")
                    ->action('Ir al Dashboard de Nexus', url('/admin/sync'))
                    ->line('Por favor, revise la consola de telemetría para más detalles.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->alertType,
            'message' => $this->message,
        ];
    }
}
