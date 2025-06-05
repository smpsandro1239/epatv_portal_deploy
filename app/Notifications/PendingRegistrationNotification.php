<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class PendingRegistrationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
            ->subject('Novo Registo Pendente - Portal EPATV')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('Um novo ex-aluno registou-se no Portal de Empregos EPATV e aguarda aprovação.')
            ->line('Nome: ' . $this->user->name)
            ->line('Email: ' . $this->user->email)
            ->line('Ano de conclusão: ' . $this->user->course_completion_year)
            ->action('Aprovar Registo', url('/admin/users/pending'))
            ->line('Obrigado por gerir o Portal de Empregos EPATV!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Novo Registo Pendente',
            'message' => 'O ex-aluno ' . $this->user->name . ' registou-se e aguarda aprovação.',
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'type' => 'pending_registration',
        ];
    }
}
