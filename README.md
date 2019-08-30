# Бэкенд "Wi-Fi в метро" [![Build Status](https://local.thedrhax.pw/jenkins/job/backend/badge/icon)](https://local.thedrhax.pw/jenkins/job/backend/) [![](https://images.microbadger.com/badges/image/thedrhax/mosmetro-backend.svg)](https://hub.docker.com/r/thedrhax/mosmetro-backend)

Данный проект предназначен для обеспечения важных для приложения "Wi-Fi в метро" серверных функций:

* сбор и обработка анонимной статистики
* сбор информации об актуальных ветках обновления с [GitHub](https://github.com/mosmetro-android/mosmetro-android/releases) и [Jenkins](https://jenkins.thedrhax.pw/job/MosMetro-Android/) и передача этой информации в подходящем для приложения виде
* обеспечение доступа к сборкам, расположенным на заблокированных доменах (jenkins.thedrhax.pw)

Так как бэкенд состоит из нескольких компонентов, которые нуждаются в предварительной настройке, весь проект был упакован в Docker-контейнеры. Это также позволило автоматически развёртывать сервер на кластере, построенном на базе Rancher.

## Как собрать?

Для того, чтобы запустить тестовый сервер на своём компьютере, вам понадобятся:

* Python 3.6+

### Сборка

Процедура сборки контейнера не отличается от стандартной:

```
docker build -t thedrhax/mosmetro-backend .
```

### Тестирование

```
python3 -m venv .py
source .py/bin/activate
pip3 install -r requirements.txt
python3 -m app
```

Сервер появится на порту 8000 и будет отслеживать изменения в коде в реальном времени.