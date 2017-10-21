# Бэкенд "Wi-Fi в метро" [![Build Status](https://local.thedrhax.pw/jenkins/job/backend/badge/icon)](https://local.thedrhax.pw/jenkins/job/backend/) [![](https://images.microbadger.com/badges/image/thedrhax/mosmetro-backend.svg)](https://hub.docker.com/r/thedrhax/mosmetro-backend)

Данный проект предназначен для обеспечения важных для приложения "Wi-Fi в метро" серверных функций:

* сбор и обработка анонимной статистики
* сбор информации об актуальных ветках обновления с [GitHub](https://github.com/mosmetro-android/mosmetro-android/releases) и [Jenkins](https://jenkins.thedrhax.pw/job/MosMetro-Android/) и передача этой информации в подходящем для приложения виде
* обеспечение доступа к сборкам, расположенным на заблокированных доменах (jenkins.thedrhax.pw)

Так как бэкенд состоит из нескольких компонентов, которые нуждаются в предварительной настройке, весь проект был упакован в Docker-контейнеры. Это также позволило автоматически развёртывать сервер на кластере, построенном на базе Rancher.

## Как собрать?

Для того, чтобы запустить тестовый сервер на своём компьютере, вам понадобятся:

* [docker](https://www.docker.com/)
* [docker-compose](https://docs.docker.com/compose/)

### Тестирование

```
. env.sh dev
docker-compose up -d
```

Сервер появится на порту 80 и будет отслеживать изменения в коде в реальном времени.

#### Окружение вместе со статистикой

Данный вариант дополнительно запустит Grafana, Chronograf и другие контейнеры для отладки статистики.

```
. env.sh dev stat
docker-compose up -d
```

### Развёртывание

В боевых условиях проект запускается на базе веб-сервера NGINX и сервера приложений uWSGI. Аналогичное окружение можно получить и в рамках тестирования, выполнив следующие команды:

```
. env.sh prod
docker-compose up -d
```

Сервер появится на порту 80, но в отличие от тестового варианта не будет отслеживать изменения исходного кода.

### Очистка системы

```
docker-compose down --rmi all -v
```

## Как оно оказывается на сервере?

Сразу после появления нового коммита в этом репозитории, Jenkins собирает образ thedrhax/mosmetro-backend и отправляет его на [Docker Hub](https://hub.docker.com/r/thedrhax/mosmetro-backend/). Затем скрипт запускает rancher-compose, который активирует обновление сервисов в Rancher. Готово :)

Процесс сборки полностью описан в [Jenkinsfile](./Jenkinsfile).
