#!/usr/bin/env python
# -*- coding: UTF-8 -*-

import requests_cache
import datetime
import redis


class CachedRequests():
    def __enter__(self, ttl=30*60):
        requests_cache.install_cache(
            cache_name='mosmetro',
            backend='redis',
            expire_after=datetime.timedelta(seconds=ttl),
            connection=redis.StrictRedis(host='redis'))

    def __exit__(self, type, value, traceback):
        requests_cache.uninstall_cache()
