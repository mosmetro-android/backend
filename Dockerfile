FROM python:3-alpine

MAINTAINER Dmitry Karikh <the.dr.hax@gmail.com>

# Install uWSGI
RUN apk --no-cache add gcc linux-headers musl-dev \
 && pip install uwsgi \
 && apk del gcc linux-headers musl-dev

# Add app files and install dependencies
ADD app /app
RUN pip install -r /app/requirements.txt

# Install NGINX and supervisor
RUN apk --no-cache add nginx supervisor
ADD container /

EXPOSE 80
CMD ["/usr/bin/supervisord"]
