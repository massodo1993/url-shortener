# URL Shortener (Laravel 12 + Filament v3)

Сервис коротких ссылок с личным кабинетом на Filament: регистрация/вход, создание
коротких ссылок, редирект с фиксацией переходов (IP + дата/время), статистика по каждой ссылке.

## Быстрый старт (Docker, одна команда)

```bash
docker compose up -d --build
docker compose logs -f app   # дождаться "[entrypoint] Ready."
```
Открыть http://localhost:8000/app регистрация/вход. Демо-логин: `admin@example.com` / `password`.

## Стек
Laravel 12, Filament v3, PostgreSQL 16, Redis 7, php-fpm 8.4, nginx 1.27, sqids/sqids.
