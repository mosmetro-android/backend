#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .config import config

import statsd


client = statsd.StatsClient(config['statsd'], 8125,
                            prefix=config['statsd_prefix'])


def escape(input):
    replace = [(x, '-') for x in [',', '.']]
    return 'null' if input is None else str(input).translate(replace)


def increment(path, name):
    client.incr(path + escape(name))
