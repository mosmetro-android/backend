FROM python:3-alpine

MAINTAINER Dmitry Karikh <the.dr.hax@gmail.com>

# Install uWSGI
RUN apk --no-cache add gcc linux-headers musl-dev \
 && pip install uwsgi \
 && apk del gcc linux-headers musl-dev

# Install dependencies
ADD requirements.txt /app/
RUN pip install -r /app/requirements.txt

# Add app files
ADD app /app/app
ADD templates /app/templates
ADD uwsgi.ini /app/

# Install NGINX and supervisor
RUN apk --no-cache add nginx supervisor
ADD container /

EXPOSE 80
CMD ["/usr/bin/supervisord"]
