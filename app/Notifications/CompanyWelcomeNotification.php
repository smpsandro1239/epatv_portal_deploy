<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bem-vinda ao Portal de Empregos EPATV')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('Bem-vinda ao Portal de Empregos da Escola Profissional Amar Terra Verde!')
            ->line('O registo da empresa ' . $notifiable->company_name . ' foi concluído com sucesso.')
            ->line('Agora pode publicar ofertas de emprego, receber candidaturas de ex-alunos qualificados e gerir todo o processo de recrutamento através da nossa plataforma.')
            ->action('Publicar Oferta', url('/company/jobs/create'))
            ->line('Obrigado por fazer parte da rede de parceiros da EPATV!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Bem-vinda ao Portal EPATV',
            'message' => 'O registo da empresa ' . $notifiable->company_name . ' foi concluído com sucesso.',
            'type' => 'welcome_company',
        ];
    }
}
