<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ManagerJoinRequestNotification extends Notification
{
    use Queueable;

    public $applicantName;
    public $type; // 'teacher' or 'parent'

    public function __construct($applicantName, $type)
    {
        $this->applicantName = $applicantName;
        $this->type = $type;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $label = ($this->type === 'teacher') ? 'معلم' : 'ولي أمر';
        $route = ($this->type === 'teacher') ? route('manager.dashboard') : route('manager.join-requests');

        return [
            'title'   => 'طلب انضمام جديد',
            'message' => "هناك طلب انضمام جديد من $label: " . $this->applicantName,
            'url'     => $route,
            'icon'    => 'bi-person-plus-fill'
        ];
    }
}

