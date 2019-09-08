#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from flask import Blueprint, request
from prometheus_client import Counter, Gauge


v2 = Blueprint('v2', __name__)


metric_connect = Counter('mosmetro_connect',
                         'Total number of connections',
                         ['branch',
                          'version',
                          'provider',
                          'connected'])

metric_duration = Gauge('mosmetro_connect_duration',
                        'Provider execution time',
                        ['branch',
                         'version',
                         'provider',
                         'connected'])

metric_mmv2 = Counter('mosmetro_connect_mmv2',
                      'Total number of MosMetroV2 connections',
                      ['branch',
                       'version',
                       'segment',
                       'mmv3_bypass',
                       'unbanned'])

metric_mmv3 = Counter('mosmetro_connect_mmv3',
                      'Total number of MosMetroV3 connections',
                      ['branch',
                       'version',
                       'next_provider'])


@v2.route("/stats", methods=['POST'])
def statistics():
    branch: str = request.form.get('build_branch')
    build: int = int(request.form.get('build_number'))
    version: int = int(request.form.get('version_code'))
    provider: str = request.form.get('provider')
    connected: bool = request.form.get('success') == 'true'
    duration: str = request.form.get('duration')

    if branch not in ['play', 'beta']:
        version = build

    labels = [branch, version, provider, connected]
    metric_connect.labels(*labels).inc()

    if provider == 'MosMetroV3':
        next_provider: str = request.form.get('switch')
        labels = [branch, version, next_provider]
        metric_mmv3.labels(*labels).inc()
        provider = next_provider

    if provider in ['MosMetroV2', 'MosMetroV2WV']:
        segment: str = request.form.get('segment') or 'unknown'
        mmv3_bypass: bool = request.form.get('v3_bypass') == 'true'
        ban_count: str = request.form.get('ban_count')

        if ban_count is not None and ban_count.isdigit():
            unbanned = int(ban_count) > 0
        else:
            unbanned = None

        labels = [branch, version, segment, mmv3_bypass, unbanned]
        metric_mmv2.labels(*labels).inc()

    if duration is not None:
        labels = [branch, version, provider, connected]
        if duration.isdigit():
            metric_duration.labels(*labels).set(int(duration))

    return ''
