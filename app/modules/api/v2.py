#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from ..util.stats import increment

from flask import Blueprint, request


v2 = Blueprint('v2', __name__)


@v2.route("/stats", methods=['POST'])
def statistics():
    build_branch = request.form.get('build_branch')
    build_number = int(request.form.get('build_number'))
    version_code = int(request.form.get('version_code'))
    provider = request.form.get('provider')

    # Build number is 0 on branch 'play' and in Google Play
    if build_number == 0:
        version = version_code
    else:
        version = build_number

    # Base name for all metrics from this client
    common = '{0}.'.format(provider)
    by_version = '{0}.{1}.{2}.'.format(build_branch, version, provider)

    # Common metrics
    increment('success', request.form.get('success'))
    increment('domain', request.environ.get('HTTP_HOST'))

    increment(common + 'success', request.form.get('success'))
    increment(by_version + 'success', request.form.get('success'))

    # Additional metrics for MosMetroV2
    if provider == "MosMetroV2":
        increment(common + 'segment', request.form.get('segment'))
        increment(by_version + 'segment', request.form.get('segment'))

        increment(common + 'ban_bypass', request.form.get('ban_bypass'))
        increment(by_version + 'ban_bypass', request.form.get('ban_bypass'))

        banned_before = str(int(request.form.get('ban_count')) > 0).lower()
        increment(common + 'banned_before', banned_before)
        increment(by_version + 'banned_before', banned_before)

    return ''
