<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bug extends Model
{
    /*
     * СТАТУСЫ:
     * 1 - Открыт
     * 2 - В работе
     * 3 - Исправлен
     * 4 - Переоткрыт
     * 5 - Закрыт
     * 6 - Отложен
     * 7 - Заблокирован
     * 8 - Отклонён
     * 9 - Не воспроизводится
     * 10 - Требует корректировки
     * 11 - Неактуально
     *
     * ТИПЫ:
     * 1 - Падение приложения
     * 2 - Зависание приложения
     * 3 - Неработающая функциональность
     * 4 - Потеря данных
     * 5 - Производительность
     * 6 - Косметическое несоответствие
     * 7 - Ошибка в тексте
     * 8 - Пожелание
     *
     * ПРИОРИТЕТЫ:
     * 1 - Низкий
     * 2 - Средний
     * 3 - Высокий
     * 4 - Критический
     * 5 - Уязвимость
     * */
    protected $guarded = [];
    public $timestamps = true;
    protected $table = 'bugs';

    public static $statuses = ['Открыт',
        'В работе',
        'Исправлен',
        'Переоткрыт',
        'Закрыт',
        'Отложен',
        'Заблокирован',
        'Отклонён',
        'Не воспроизводится',
        'Требует корректировки',
        'Неактуально'];

    public function getStatus()
    {
        return Bug::$statuses[$this->status];
    }

    public function getStatusColor()
    {
        switch ($this->status + 1) {
            default:
            case 1:
            case 4:
                return 'primary';
            case 2:
            case 6:
                return 'warning';
            case 3:
            case 5:
                return 'success';
            case 7:
            case 8:
            case 9:
            case 10:
            case 11:
                return 'danger';
        }
    }

    public static $types = ['Падение приложения',
        'Зависание приложения',
        'Неработающая функциональность',
        'Потеря данных',
        'Производительность',
        'Косметическое несоответствие',
        'Ошибка в тексте',
        'Пожелание'];

    public function getType()
    {
        return Bug::$types[$this->type];
    }

    public static $priorities = ['Низкий',
        'Средний',
        'Высокий',
        'Критический',
        'Уязвимость'];

    public function getPriority()
    {
        return Bug::$priorities[$this->priority];
    }

    public function getAuthor()
    {
        return $this->belongsTo(User::class, 'author', 'user_id');
    }

    public function getBugUpdates()
    {
        return $this->hasMany(BugUpdate::class, 'bug_id', 'id')->orderBy('time', 'desc');
    }

    public function getProduct()
    {
        return $this->belongsTo(Product::class, 'product');
    }

    public function getProductVersion()
    {
        $ver = ProductUpdate::find($this->version);
        if($ver == null) return null;
        return $ver->version;
    }

    public function canBeReopened() {
        return in_array($this->status, [8,9]);
    }

    public function isActualVersion()
    {
        return !$this->canBeReopened() || $this->version >= $this->getProduct->getProductVersions()->orderBy('id', 'DESC')->first()->id;
    }
}
