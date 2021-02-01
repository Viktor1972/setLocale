# setLocale
The Laravel Middleware class for using multilingual content

php artisan make:middleware LocaleMiddleware

app\Http\Kernel.php in $middleware add:
\App\Http\Middleware\LocaleMiddleware::class,

routes\web.php
Route::group(['prefix' => LocaleMiddleware::getLocale()], function(){
    
    // your route here
    
});

//set locale
Route::get('setlocale/{lang}', function ($lang) {
    return redirect(LocaleMiddleware::setLocale($lang));
})->name('setlocale');

// Templates:
// To change lang:
  <a href="<?= route('setlocale', ['lang' => 'en']) ?>">English</a>
  <a href="<?= route('setlocale', ['lang' => 'ru']) ?>">Русский</a>
  <a href="<?= route('setlocale', ['lang' => 'ua']) ?>">Українська</a>
  
 // get locale:
{{ app()->getLocale() }}

