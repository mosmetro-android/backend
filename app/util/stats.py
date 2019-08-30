#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .config import config

import statsd


client = statsd.StatsClient(config['statsd'], 8125,
                            prefix=config['statsd_prefix'])


def escape(input):
    if input is None:
        return 'null'

    input = str(input)
    input = input.replace(',', '-')
    input = input.replace('.', '-')
    return input


def increment(path, name):
    client.incr("{0}.{1}".format(path, escape(name)))


def gauge(path, value):
    try:
        client.gauge(path, int(value))
    except TypeError:
        return
