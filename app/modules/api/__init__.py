#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .admin import admin
from .v1 import v1


def register(app):
    app.register_blueprint(admin, url_prefix='/api/admin')
    app.register_blueprint(v1, url_prefix='/api/v1')
