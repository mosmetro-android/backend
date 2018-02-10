#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .jenkins import Jenkins
from .github import GitHub

from ..util.config import config

import json
import redis

from redis_lock import Lock


cache = redis.StrictRedis(host=config['redis'])


def generate():
    branches = dict()
    branches.update(Jenkins('https://jenkins.thedrhax.pw', 'MosMetro-Android'))
    if branches.get('play'):  # Branch 'play' must be loaded from GitHub
        del branches['play']
    branches.update(GitHub('mosmetro-android', 'mosmetro-android'))
    return branches


def replace():
    cache.set('branches', json.dumps(generate()), ex=6*60*60)  # 6 hours TTL


def get():
    if not cache.exists('branches'):
        with Lock(cache, 'lock-branches'):
            if not cache.exists('branches'):
                replace()

    return json.loads(cache.get('branches'))
