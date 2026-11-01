<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TeacherReplyNotification extends Notification
{
    use Queueable;

    public $childName;
    public $reply;
    public $childId;

    public function __construct($childName, $reply, $childId = null)
    {
        $this->childName = $childName;
        $this->reply = $reply;
        $this->childId = $childId;
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
            'url'     => $this->childId ? route('parent.child.show', $this->childId) . '?open_history=1' : route('parent.dashboard'),
            'icon'    => 'bi-reply-fill'
        ];
    }
}

