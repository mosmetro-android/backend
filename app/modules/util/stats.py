#!/usr/bin/env python
# -*- coding: UTF-8 -*-

import statsd

client = statsd.StatsClient('statsd', 8125, prefix='mosmetro')


def escape(input):
    replace = [(x, '-') for x in [',', '.']]
    return 'null' if input is None else str(input).translate(replace)


def increment(path, name):
    client.incr(path + escape(name))
