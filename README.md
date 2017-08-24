# Бэкенд "Wi-Fi в метро" [![Build Status](https://local.thedrhax.pw/jenkins/job/backend/badge/icon)](https://local.thedrhax.pw/jenkins/job/backend/) [![](https://images.microbadger.com/badges/image/thedrhax/mosmetro-backend.svg)](https://hub.docker.com/r/thedrhax/mosmetro-backend)

Этот набор PHP-скриптов предназначен для обеспечения работы встроенной системы обновления, а также сбора статистики (по крайней мере, так было в прошлом).

Так как PHP, Memcached и NGINX нуждаются в предварительной настройке, весь проект был упакован в Docker-контейнеры. Это также позволило автоматически развёртывать сервер с помощью Rancher.

## Что оно делает сейчас?

В данный момент у этого проекта осталась одна единственная функция — сбор информации о новых версиях и донесение её до конечных пользователей.

Информация о ветках "play" и "beta" берётся из раздела [Releases](https://github.com/mosmetro-android/mosmetro-android/releases) основного проекта. Остальные ветки (master и временные) заполняются из [Jenkins](https://local.thedrhax.pw/jenkins/job/MosMetro-Android/).

## Почему не парсить эту информацию напрямую?

За всю историю проекта сборки много раз переезжали в различных направлениях. Сейчас я могу спокойно их переносить без необходимости выкатывать полномасштабное обновление. К тому же домен Jenkins заблокирован в сети метро, а перекидывать его между доменами — настоящая боль.

## Как собрать?

Для того, чтобы запустить тестовый сервер на своём компьютере, вам понадобится:

* [docker](https://www.docker.com/)
* [docker-compose](https://docs.docker.com/compose/)

После этого можно собрать и запустить всё окружение с помощью следующей команды:

```
docker-compose -f docker-compose.test.yml up -d
```

Сервер станет доступен на порту 8080, а также будет отражать все изменения исходного кода.

Очистить систему от контейнеров, образов и томов можно, опять же, с помощью одной следующей команды:

```
docker-compose -f docker-compose.test.yml down --rmi all -v
```

## Как оно оказывается на сервере?

Сразу после появления нового коммита в этом репозитории, Jenkins собирает образ thedrhax/mosmetro-backend и отправляет его на [Docker Hub](https://hub.docker.com/r/thedrhax/mosmetro-backend/). Затем скрипт запускает rancher-compose, который активирует обновление сервисов в Rancher. Готово :)
