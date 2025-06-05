<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JobApplication;

class NewApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;

    /**
     * Create a new notification instance.
     */
    public function __construct(JobApplication $application)
    {
        $this->application = $application;
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
        $job = $this->application->job;
        
        return (new MailMessage)
            ->subject('Nova Candidatura: ' . $job->title)
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('Recebeu uma nova candidatura para a oferta "' . $job->title . '".')
            ->line('Candidato: ' . $this->application->name)
            ->line('Email: ' . $this->application->email)
            ->line('Ano de conclusão: ' . $this->application->course_completion_year)
            ->action('Ver Candidatura', url('/company/applications/' . $this->application->id))
            ->line('Obrigado por utilizar o Portal de Empregos EPATV!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $job = $this->application->job;
        
        return [
            'title' => 'Nova Candidatura',
            'message' => 'Recebeu uma nova candidatura para a oferta "' . $job->title . '".',
            'application_id' => $this->application->id,
            'job_id' => $job->id,
            'candidate_name' => $this->application->name,
            'type' => 'new_application',
        ];
    }
}
