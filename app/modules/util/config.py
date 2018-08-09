#!/usr/bin/env python
# -*- coding: UTF-8 -*-

import os


def from_env(name, fallback):
    value = os.environ.get(name)
    return value if value is not None else fallback


config = {
    "admin": from_env("MOSMETRO_ADMIN", "false"),
    "admin_username": from_env("MOSMETRO_ADMIN_USERNAME", "admin"),
    "admin_password": from_env("MOSMETRO_ADMIN_PASSWORD", "admin"),

    "redis": from_env("MOSMETRO_REDIS", "redis"),

    "statsd": from_env("MOSMETRO_STATSD", "statsd"),
    "statsd_prefix": from_env("MOSMETRO_STATSD_PREFIX", "mosmetro"),

    "jenkins": {
        "url": from_env("MOSMETRO_JENKINS_URL", "https://jenkins.thedrhax.pw"),
        "project": from_env("MOSMETRO_JENKINS_PROJECT",
                            "mosmetro-android-pipeline")
    },

    "github": {
        "user": from_env("MOSMETRO_GITHUB_USER", "mosmetro-android"),
        "repo": from_env("MOSMETRO_GITHUB_REPO", "mosmetro-android")
    }
}
