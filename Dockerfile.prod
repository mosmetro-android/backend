FROM python:3-alpine

MAINTAINER Dmitry Karikh <the.dr.hax@gmail.com>

ADD app /app
WORKDIR /app

RUN pip install -r requirements.txt

RUN apk --no-cache add gcc linux-headers musl-dev \
 && pip install uwsgi \
 && apk del gcc linux-headers musl-dev

EXPOSE 9000
CMD ["uwsgi", "uwsgi.ini"]
