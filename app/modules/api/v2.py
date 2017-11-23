#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from ..util.stats import increment

from flask import Blueprint, request


v2 = Blueprint('v2', __name__)


@v2.route("/stats", methods=['POST'])
def statistics():
    build_branch = request.form.get('build_branch')
    build_number = request.form.get('build_number')
    version_code = request.form.get('version_code')
    provider = request.form.get('provider')

    # Build number is 0 on branch 'play' and in Google Play
    if build_number == 0:
        version = version_code
    else:
        version = build_number

    # Base name for all metrics from this client
    base = '{0}.{1}.{2}.'.format(build_branch, version, provider)

    # Common metrics
    increment(base + 'success', request.form.get('success'))

    # Additional metrics for MosMetroV2
    if provider == "MosMetroV2":
        increment(base + 'segment', request.form.get('segment'))
        increment(base + 'ban_bypass', request.form.get('ban_bypass'))
        increment(base + 'ban_count', request.form.get('ban_count'))

    return ''
