<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TeacherAssignedNotification extends Notification
{
    use Queueable;

    public $className;

    public function __construct($className)
    {
        $this->className = $className;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // تخزين في قاعدة البيانات ليظهر في النافبار
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'    => 'تم إسناد صف دراسي جديد',
            'message'  => 'قام مدير المدرسة بربطك بصف دراسي جديد: (' . $this->className . ').',
            'url'      => route('teacher.classes'), // الرابط عند الضغط على الإشعار
            'icon'     => 'bi-journal-check'
        ];
    }
}