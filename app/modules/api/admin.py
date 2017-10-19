#!/usr/bin/env python
# -*- coding: UTF-8 -*-

import redis
import json

from functools import wraps
from flask import Blueprint, Response, request
admin = Blueprint('admin', __name__)


def check_auth(username, password):
    return username == "admin" and password == "SoSecureMuchWow"


def authenticate():
    return Response('Could not verify your access level for that URL.\n'
                    'You have to login with proper credentials', 401,
                    {'WWW-Authenticate': 'Basic realm="Login Required"'})


def requires_auth(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        auth = request.authorization
        if not auth or not check_auth(auth.username, auth.password):
            return authenticate()
        return f(*args, **kwargs)
    return decorated


@admin.route("/flush")
@requires_auth
def flush():
    conn = redis.StrictRedis(host='redis')
    conn.flushall()
    return json.dumps({'status': 'success'})
