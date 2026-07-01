<?php

use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

/*
| Личный кабинет реализован на Filament: /app  (login, register, ресурсы).
| Роут ниже — публичный редирект по короткому коду.
*/

Route::get('/', fn () => redirect('/app'));

/*
| ВАЖНО: этот роут должен быть ПОСЛЕДНИМ, чтобы не перехватывать /app и т.п.
| Ограничение: код — латиница/цифры длиной 3–16.
*/
Route::get('/{code}', RedirectController::class)
    ->where('code', '[A-Za-z0-9]{3,16}')
    ->name('short.redirect');
