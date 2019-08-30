#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from ..util.stats import increment, gauge

from flask import Blueprint, request


v2 = Blueprint('v2', __name__)


def mosmetrov2(prefix):
    increment(prefix + 'segment', request.form.get('segment'))
    increment(prefix + 'v3_bypass', request.form.get('v3_bypass'))

    banned_before = str(int(request.form.get('ban_count')) > 0).lower()
    increment(prefix + 'banned_before', banned_before)


def mosmetrov3(prefix):
    increment(prefix + 'switch', request.form.get('switch'))
    increment(prefix + 'override', request.form.get('override'))

    if request.form.get('switch') == 'MosMetroV2':
        mosmetrov2(prefix)
        mosmetrov2(prefix + 'MosMetroV2.')


@v2.route("/stats", methods=['POST'])
def statistics():
    build_branch = request.form.get('build_branch')
    build_number = int(request.form.get('build_number'))
    version_code = int(request.form.get('version_code'))
    provider = request.form.get('provider')

    if build_branch in ['play', 'beta']:
        version = version_code
    else:
        version = build_number

    # Base name for all metrics from this client
    common = '{0}.'.format(provider)
    by_version = '{0}.{1}.{2}.'.format(build_branch, version, provider)

    # Common metrics
    increment('domain', request.environ.get('HTTP_HOST'))

    increment('version.name', request.form.get('version_name'))
    increment('version.code', version_code)

    increment('success', request.form.get('success'))
    increment(common + 'success', request.form.get('success'))
    increment(by_version + 'success', request.form.get('success'))

    gauge(common + 'duration', request.form.get('duration'))
    gauge(by_version + 'duration', request.form.get('duration'))

    # Additional metrics for MosMetroV2
    if provider == 'MosMetroV2' or provider == 'MosMetroV2WV':
        mosmetrov2(common)
        mosmetrov2(by_version)

    # Additional metrics for MosMetroV3
    if provider == 'MosMetroV3':
        mosmetrov3(common)
        mosmetrov3(by_version)

    return ''
