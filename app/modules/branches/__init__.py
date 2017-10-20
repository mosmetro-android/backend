#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .jenkins import Jenkins
from .github import GitHub

import json
import redis


cache = redis.StrictRedis(host='redis')


def generate():
    branches = dict()
    branches.update(Jenkins('https://jenkins.thedrhax.pw', 'MosMetro-Android'))
    del branches['play']  # Branch 'play' must be loaded from GitHub
    branches.update(GitHub('mosmetro-android', 'mosmetro-android'))
    return branches


def replace():
    cache.set('branches', json.dumps(generate()), ex=6*60*60)  # 6 hours TTL


def get():
    if not cache.exists('branches'):
        replace()

    return json.loads(cache.get('branches'))
