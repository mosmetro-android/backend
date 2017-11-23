#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .admin import admin
from .v1 import v1
from .v2 import v2
from .releases import releases

from ..util.config import config


def register(app):
    if config['admin'] == "true":
        app.register_blueprint(admin, url_prefix='/api/admin')

    app.register_blueprint(v1, url_prefix='/api/v1')
    app.register_blueprint(v2, url_prefix='/api/v2')
    app.register_blueprint(releases, url_prefix='/releases')
