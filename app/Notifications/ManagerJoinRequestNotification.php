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
    
    // كلاهما يجب أن يوجها إلى صفحة المهام الموحدة للمدير
    $route = route('manager.tasks');

    return [
        'title'   => 'طلب انضمام جديد',
        'message' => "هناك طلب انضمام جديد من $label: " . $this->applicantName,
        'url'     => $route,
        'icon'    => 'bi-person-plus-fill'
    ];
}
public function readNotification($id)
{
    $this->checkManager();
    
    $notification = auth()->user()->notifications()->where('id', $id)->first();

    if ($notification) {
        $notification->markAsRead();
    }

    // التوجيه المباشر وثابت إلى صفحة المهام حصراً لمنع أخطاء 404
    return redirect()->route('manager.tasks');
}
}

