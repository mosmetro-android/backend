#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .config import config

import requests_cache
import datetime
import redis


class CachedRequests(object):
    def __init__(self, ttl=30*60):
        self.ttl = ttl

    def __enter__(self):
        requests_cache.install_cache(
            cache_name='mosmetro',
            backend='redis',
            expire_after=datetime.timedelta(seconds=self.ttl),
            connection=redis.StrictRedis(host=config['redis']))

    def __exit__(self, type, value, traceback):
        requests_cache.uninstall_cache()
