<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TeacherReplyNotification extends Notification
{
    use Queueable;

    public $childName;
    public $reply;

    public function __construct($childName, $reply)
    {
        $this->childName = $childName;
        $this->reply = $reply;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'رد جديد من المعلمة',
            'message' => 'قامت المعلمة بالرد على ملاحظتك بخصوص الطالب: ' . $this->childName,
            'reply'   => $this->reply,
            'url'     => route('parent.dashboard'),
            'icon'    => 'bi-reply-fill'
        ];
    }
}

