<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentWelcomeNotification extends Notification implements ShouldQueue
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
            ->subject('Bem-vindo ao Portal de Empregos EPATV')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('Bem-vindo ao Portal de Empregos da Escola Profissional Amar Terra Verde!')
            ->line('O seu registo foi concluído com sucesso e já pode aceder a todas as funcionalidades do portal.')
            ->line('Explore as ofertas de emprego disponíveis, candidate-se às que mais se adequam ao seu perfil e acompanhe o estado das suas candidaturas.')
            ->action('Explorar Ofertas', url('/jobs'))
            ->line('Obrigado por fazer parte da comunidade EPATV!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Bem-vindo ao Portal EPATV',
            'message' => 'O seu registo foi concluído com sucesso. Explore as ofertas disponíveis!',
            'type' => 'welcome',
        ];
    }
}
