#!/usr/bin/env python
# -*- coding: UTF-8 -*-

import redis
import json

from flask import Blueprint
admin = Blueprint('admin', __name__)


@admin.route("/flush")
def flush():
    conn = redis.StrictRedis(host='redis')
    conn.flushall()
    return json.dumps({'status': 'success'})
