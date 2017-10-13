#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .admin import admin
from .v1 import v1
from .releases import releases


def register(app):
    app.register_blueprint(admin, url_prefix='/api/admin')
    app.register_blueprint(v1, url_prefix='/api/v1')
    app.register_blueprint(releases, url_prefix='/releases')
