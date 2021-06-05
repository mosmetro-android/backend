FROM alpine:3.13

# Install packages
RUN apk --no-cache add python3 uwsgi-python3 nginx supervisor py3-psycopg2 py3-pip

# Install dependencies
ADD requirements.txt /app/
RUN pip3 install -r /app/requirements.txt

# Add app files
ADD app /app/app/
ADD migrations /app/migrations/
ADD uwsgi.ini migrations.json /app/
ADD container /

EXPOSE 80 9100
CMD ["/usr/bin/supervisord"]
