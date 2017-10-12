#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .jenkins import Jenkins
from .github import GitHub

branches = dict()
branches.update(Jenkins('https://jenkins.thedrhax.pw', 'MosMetro-Android'))
del branches['play']  # Branch 'play' must be loaded from GitHub
branches.update(GitHub('mosmetro-android', 'mosmetro-android'))


def get():
    return branches
