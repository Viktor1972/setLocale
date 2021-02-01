<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class LocaleMiddleware
{
    public static $mainLanguage = 'ua'; //основной язык, который не должен отображаться в URl
    public static $languages = ['en', 'ru', 'ua']; // Указываем, какие языки будем использовать в приложении.

    /*
    * Проверяет наличие корректной метки языка в текущем URL
    * Возвращает метку или значеие null, если нет метки
    */
    public static function getLocale() {
        $uri = Request::path(); //получаем URI
        $segmentsURI = explode('/',$uri); //делим на части по разделителю "/"

        //Проверяем метку языка  - есть ли она среди доступных языков
        if (!empty($segmentsURI[0]) && in_array($segmentsURI[0], self::$languages)) {

            if ($segmentsURI[0] != self::$mainLanguage) return $segmentsURI[0];

        }

        return null;
    }

    /*
    * Устанавливает язык приложения в зависимости от метки языка из URL
    */
    public function handle($request, Closure $next) {
        $locale = self::getLocale();

        if($locale) App::setLocale($locale);

        //если метки нет - устанавливаем основной язык $mainLanguage
        else App::setLocale(self::$mainLanguage);

        return $next($request); //пропускаем дальше - передаем в следующий посредник
    }

    /**
     * Выбираем ссылку для переадресации с другим языком
     *
     * @param $lang
     * @return string
     */
    public static function setLocale($lang) {
        $referer = Redirect::back()->getTargetUrl(); //URL предыдущей страницы
        $parse_url = parse_url($referer, PHP_URL_PATH); //URI предыдущей страницы

        //разбиваем на массив по разделителю
        $segments = explode('/', $parse_url);

        //Если URL (где нажали на переключение языка) содержал корректную метку языка
        if (in_array($segments[1], self::$languages)) {

            unset($segments[1]); //удаляем метку
        }

        //Добавляем метку языка в URL (если выбран не язык по-умолчанию)
        if ($lang != self::$mainLanguage){
            array_splice($segments, 1, 0, $lang);
        }

        //формируем полный URL
        $url = implode("/", $segments);

        //если были еще GET-параметры - добавляем их
        if(parse_url($referer, PHP_URL_QUERY)){
            $url = $url.'?'. parse_url($referer, PHP_URL_QUERY);
        }

        return $url;
    }
}
