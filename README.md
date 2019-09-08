# Бэкенд "Wi-Fi в метро" [![](https://images.microbadger.com/badges/image/thedrhax/mosmetro-backend.svg)](https://hub.docker.com/r/thedrhax/mosmetro-backend)

Данный проект предназначен для обеспечения важных для приложения "Wi-Fi в метро" серверных функций:

* сбор и обработка анонимной статистики
* сбор информации об актуальных ветках обновления с [GitHub](https://github.com/mosmetro-android/mosmetro-android/releases) и [Jenkins](https://jenkins.thedrhax.pw/job/MosMetro-Android/) и передача этой информации в подходящем для приложения виде
* обеспечение доступа к сборкам, расположенным на заблокированных доменах (jenkins.thedrhax.pw)

Так как бэкенд состоит из нескольких компонентов, которые нуждаются в предварительной настройке, весь проект был упакован в Docker-контейнер.

## Как запустить?

Для того, чтобы запустить тестовый сервер на своём компьютере, вам понадобится Python 3.6+.

```
# Создать виртуальную среду
python3 -m venv .py
source .py/bin/activate

# Запустить Redis (необязательно в Docker, но так проще)
docker run -d --name mosmetro-redis -p 6379:6379 redis:5-alpine

# Установить зависимости
pip3 install -r requirements.txt

# Запустить приложение
python3 -m app
```

Сервер появится на порту 8000 и будет отслеживать изменения в коде в реальном времени.

В тестовом режиме метрики Prometheus также доступны на порту 8000 по пути `/metrics`.

## Как собрать?

Процедура сборки контейнера не отличается от стандартной:

```
docker build -t thedrhax/mosmetro-backend .
```
