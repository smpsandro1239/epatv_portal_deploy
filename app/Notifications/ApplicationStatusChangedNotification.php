<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JobApplication;

class ApplicationStatusChangedNotification extends Notification implements ShouldQueue
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
        $company = $job->company;
        $status = $this->application->status;
        
        $mailMessage = (new MailMessage)
            ->subject('Atualização da sua candidatura: ' . $job->title)
            ->greeting('Olá ' . $notifiable->name . ',');
            
        if ($status === 'accepted') {
            $mailMessage->line('Parabéns! A sua candidatura para a oferta "' . $job->title . '" foi aceite.')
                ->line('A empresa ' . $company->company_name . ' demonstrou interesse no seu perfil.')
                ->line('Poderá ser contactado em breve para mais informações sobre o processo de recrutamento.');
        } elseif ($status === 'rejected') {
            $mailMessage->line('Lamentamos informar que a sua candidatura para a oferta "' . $job->title . '" não foi selecionada.')
                ->line('A empresa ' . $company->company_name . ' agradece o seu interesse, mas optou por outros candidatos nesta fase.')
                ->line('Não desanime e continue a explorar outras oportunidades no nosso portal!');
        }
        
        return $mailMessage
            ->action('Ver Detalhes', url('/student/applications'))
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
        $status = $this->application->status;
        
        $title = $status === 'accepted' ? 'Candidatura Aceite' : 'Candidatura Não Selecionada';
        $message = $status === 'accepted' 
            ? 'A sua candidatura para "' . $job->title . '" foi aceite!' 
            : 'A sua candidatura para "' . $job->title . '" não foi selecionada.';
        
        return [
            'title' => $title,
            'message' => $message,
            'application_id' => $this->application->id,
            'job_id' => $job->id,
            'company_name' => $job->company->company_name,
            'status' => $status,
            'type' => 'application_status_changed',
        ];
    }
}
