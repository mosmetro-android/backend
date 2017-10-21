#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .. import branches
from ..util.config import config

import json

from functools import wraps
from flask import Blueprint, Response, request


admin = Blueprint('admin', __name__)


def check_auth(username, password):
    if username == config['admin_username']:
        if password == config['admin_password']:
            return True
    return False


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
    branches.replace()
    return json.dumps(branches.get())
