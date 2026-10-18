<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Child;

class ChildAssignedNotification extends Notification
{
    use Queueable;

    public $child;

    public function __construct(Child $child)
    {
        $this->child = $child;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

public function toArray(object $notifiable): array
{
    $subject = $this->record->subject ?? $this->record->topic ?? 'سجل جديد';

    return [
        'title'    => 'سجل يومي جديد للطفل: ' . $this->child->name,
        'message'  => 'تم إضافة ملاحظة/درجة جديدة في مادة (' . $subject . ') لطفلك ' . $this->child->name . '.',
        'child_id' => $this->child->id,
        'url'      => route('parent.child.show', $this->child->id), // أو مسار قراءة الإشعار إن وجد
        'icon'     => 'bi-journal-text'
    ];
}
}