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

    public function getStatus()
    {
        switch ($this->status) {
            default:
            case 1:
                return 'Открыт';
            case 2:
                return 'В работе';
            case 3:
                return 'Исправлен';
            case 4:
                return 'Переоткрыт';
            case 5:
                return 'Закрыт';
            case 6:
                return 'Отложен';
            case 7:
                return 'Заблокирован';
            case 8:
                return 'Отклонён';
            case 9:
                return 'Не воспроизводится';
            case 10:
                return 'Требует корректировки';
            case 11:
                return 'Неактуально';
        }
    }

    public function getType()
    {
        switch ($this->type) {
            default:
            case 1:
                return 'Падение приложения';
            case 2:
                return 'Зависание приложения';
            case 3:
                return 'Неработающая функциональность';
            case 4:
                return 'Потеря данных';
            case 5:
                return 'Производительность';
            case 6:
                return 'Косметическое несоответствие';
            case 7:
                return 'Ошибка в тексте';
            case 8:
                return 'Пожелание';
        }
    }

    public function getPriority()
    {
        switch ($this->priority) {
            default:
            case 1:
                return 'Низкий';
            case 2:
                return 'Средний';
            case 3:
                return 'Высокий';
            case 4:
                return 'Критический';
            case 5:
                return 'Уязвимость';
        }
    }

    public function getAuthor()
    {
        return $this->belongsTo(User::class, 'author', 'user_id');
    }

    public function getBugUpdates()
    {
        return $this->hasMany(BugUpdate::class, 'bug_id', 'id');
    }

    public function getTag()
    {
        return $this->belongsTo(Tag::class, 'tag', 'id');
    }

    public function getProduct()
    {
        return $this->belongsTo(Product::class, 'product');
    }

    public function isActualVersion()
    {
        if ($this->version >= $this->getProduct->getProductVersions()->orderBy('id', 'DESC')->first()->id) return true; else return false;
    }
}
