<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Child;

class DailyChangeAddedNotification extends Notification
{
    use Queueable;

    public $child;
    public $record;

    /**
     * استقبال بيانات الطفل والسجل اليومي عند إنشاء الإشعار
     */
    public function __construct(Child $child, $record)
    {
        $this->child = $child;
        $this->record = $record;
    }

    /**
     * تحديد قناة التوصيل (التخزين في قاعدة البيانات للموقع)
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // تخزين الإشعار في قاعدة البيانات ليظهر في النافبار
    }

    /**
     * تجهيز مصفوفة البيانات التي سيتم حفظها وقراءتها في النافبار
     *
     * @return array<string, mixed>
     */
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