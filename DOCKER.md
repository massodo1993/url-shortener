# Запуск через Docker (в одну команду)

Проект самодостаточный: содержит полный скелет Laravel 12 + `composer.json` с Filament v3.
Ничего доустанавливать локально не нужно — даже Composer. Всё ставится внутри контейнера.

## Требования
- Docker + Docker Compose (Docker Desktop на Mac/Windows или docker-ce на Linux).

## Запуск

```bash
cd url-shortener
cp .env.example .env      # опционально: entrypoint сделает это сам
docker compose up -d --build
```

При первом старте контейнер `app` автоматически:
1. выполнит `composer install` (это займёт 1–3 минуты — качаются зависимости);
2. создаст `.env` и сгенерирует `APP_KEY`;
3. дождётся PostgreSQL и накатит миграции;
4. один раз засеет демо-данные;
5. сделает `storage:link`.

Следить за прогрессом первой сборки:
```bash
docker compose logs -f app
```
Дождитесь строки `[entrypoint] Ready.` — после неё приложение доступно.

## Доступ
- Приложение: http://localhost:8000
- Личный кабинет (Filament): http://localhost:8000/app
- Демо-логин: **admin@example.com** / **password**
- PostgreSQL снаружи: `localhost:5432` (shortener / secret)

## Как проверять задачу
1. Открыть http://localhost:8000/app → **Register**, создать аккаунт (или войти демо-логином).
2. «Создать ссылку» → вставить любой `https://...` → получить короткий код.
3. Открыть короткую ссылку `http://localhost:8000/{code}` в новой вкладке → произойдёт редирект.
4. Вернуться в кабинет → у ссылки вырос счётчик кликов → «Статистика» → список переходов с IP и временем.

## Команды

```bash
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app bash                        # шелл
docker compose logs -f app                          # логи
docker compose down                                 # стоп
docker compose down -v                              # стоп + удалить том БД (полный сброс)
```

> После `down -v` удалите `storage/.seeded` (если хотите повторный автосид) —
> либо просто выполните `migrate:fresh --seed` вручную.

## Состав окружения
- **app** — php-fpm 8.4 (pdo_pgsql, redis, gd, intl, bcmath, opcache) + Composer.
- **nginx** — 1.27, отдаёт `public/`, порт 8000.
- **db** — PostgreSQL 16 с healthcheck.
- **redis** — 7 (cache/queue при желании).

## Частые вопросы
- **Порт занят.** Поменяйте `8000:80` (nginx) или `5432:5432` (db) в `docker-compose.yml`.
- **IP всех кликов = 127.0.0.1 / IP docker-сети.** Это ожидаемо за прокси; `TrustProxies` уже настроен на `*`, реальный клиентский IP берётся из `X-Forwarded-For`. Локально между контейнерами это будет адрес nginx — на реальном сервере за балансировщиком придёт настоящий IP.
- **Долгая первая сборка.** Это `composer install`. Последующие старты мгновенные (vendor кэшируется в volume проекта).
