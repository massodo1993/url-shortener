# URL Shortener (Laravel 12 + Filament v3)

Сервис коротких ссылок с личным кабинетом на Filament: регистрация/вход, создание
коротких ссылок, редирект с фиксацией переходов (IP + дата/время), статистика по каждой ссылке.

## Быстрый старт (Docker, одна команда)

```bash
docker compose up -d --build
docker compose logs -f app   # дождаться "[entrypoint] Ready."
```
Открыть http://localhost:8000/app — регистрация/вход. Демо-логин: `admin@example.com` / `password`.

Подробности, команды и как проверять задачу — см. **DOCKER.md**.

## Функциональность
- **Аутентификация** — Filament login + registration (`/app`).
- **Создание ссылок** — оригинальный URL → короткий код (`/{code}`), код детерминированно кодирует ID ссылки (Sqids), уникальность гарантируется без коллизий и без циклов проверки в БД.
- **Редирект** — `GET /{code}` → 302 на оригинал, каждый переход пишется в `clicks`.
- **Кабинет** — список своих ссылок, удаление, статистика по каждой (список переходов: IP, дата/время, referer, UA) и общий счётчик кликов; виджет с суммарными метриками.

## Архитектура (ключевое)
- `App\Models\Link` — в событии `creating` берёт `id` из sequence Postgres и кодирует его в `code` через `Sqids` (алфавит переставлен по `APP_KEY`, см. `AppServiceProvider`); аксессор `short_url`.
- `App\Models\Click` — IP, user_agent, referer, время перехода (`timestamps=false`, только `created_at`).
- `App\Http\Controllers\RedirectController` — запись клика + атомарный `increment('clicks_count')`.
- `App\Filament\Resources\LinkResource` — кабинет; `getEloquentQuery()` изолирует данные по `user_id`, поэтому пользователь видит/удаляет только свои ссылки.
- `LinkResource\Pages\LinkClicks` — страница статистики переходов.
- `LinkResource\Widgets\LinksOverview` — суммарные метрики (ссылки / переходы).
- Роут `/{code}` идёт последним и ограничен `[A-Za-z0-9]{3,16}`, чтобы не перехватывать `/app`.
- `TrustProxies` = `*` — корректный клиентский IP за nginx/балансировщиком.

## Стек
Laravel 12, Filament v3, PostgreSQL 16, Redis 7, php-fpm 8.4, nginx 1.27, sqids/sqids.
