#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from flask import Blueprint, request
from prometheus_client import Counter, Gauge


v2 = Blueprint('v2', __name__)


metric_connect = Counter(
    'mosmetro_connection',
    'Total number of connections',
    ['branch', 'version', 'success'])

metric_ssid = Counter(
    'mosmetro_ssid',
    'Number of connections per SSID',
    ['provider', 'ssid'])

metric_provider = Counter(
    'mosmetro_provider',
    'Number of successful connections per provider',
    ['branch', 'version', 'provider'])

metric_duration = Gauge(
    'mosmetro_duration',
    'Provider execution time in milliseconds',
    ['branch', 'version', 'provider'])

metric_mmv2_segment = Counter(
    'mosmetro_v2_segment',
    'Number of successful connections per MosMetroV2 segment',
    ['branch', 'version', 'segment'])

metric_switch = Counter(
    'mosmetro_provider_switch',
    'Number of switches from one provider to another',
    ['from', 'to'])


@v2.route("/stats", methods=['POST'])
def statistics():
    branch: str = request.form.get('build_branch')
    build: int = int(request.form.get('build_number'))
    version: int = int(request.form.get('version_code'))
    provider: str = request.form.get('provider')
    success: bool = request.form.get('success') == 'true'

    if branch not in ['play', 'beta']:
        version = build

    metric_connect.labels(branch, version, success).inc()
    metric_provider.labels(branch, version, provider).inc()

    if not success:
        return ''

    duration: str = request.form.get('duration')
    if duration is not None and duration.isdigit():
        metric_duration.labels(branch, version, provider).set(int(duration))

    switch: str = request.form.get('switch')
    if switch:
        metric_switch.labels(provider, switch).inc()

    segment: str = request.form.get('segment')
    if segment:
        metric_mmv2_segment.labels(branch, version, segment).inc()

    ssid: str = request.form.get('ssid')
    if ssid and ssid != '<unknown ssid>':
        metric_ssid.labels(provider, ssid).inc()

    return ''
