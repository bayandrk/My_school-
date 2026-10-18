<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Child;

class ParentNoteNotification extends Notification
{
    use Queueable;

    public $child;
    public $note;

    public function __construct(Child $child, string $note)
    {
        $this->child = $child;
        $this->note  = $note;
    }

    public function via($notifiable)
    {
        return ['database']; // يحفظ الإشعار في قاعدة البيانات ليظهر في زر الجرس
    }

    public function toArray($notifiable)
    {
        return [
            'title'     => 'ملاحظة جديدة من ولي الأمر',
            'message'   => 'أرسلت الأم ملاحظة خاصة بالطالب: ' . $this->child->name,
            'child_id'  => $this->child->id,
            'child_name'=> $this->child->name,
            'note'      => $this->note,
            'url'       => route('teacher.parent-notes'), // مسار صفحة ملاحظات الأهل للآنسة
        ];
    }
}