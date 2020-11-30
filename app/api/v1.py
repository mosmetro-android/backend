#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .. import branches

from prometheus_client import Counter
from flask import url_for, Blueprint, render_template, request, abort, jsonify


v1 = Blueprint('v1', __name__)


metric_update_check = Counter('mosmetro_update_check',
                              'Total number of update check requests')


metric_download = Counter('mosmetro_download',
                          'Total number of APK downloads',
                          ['branch', 'version'])


@v1.route("/branches.php")
def branches_php():
    metric_update_check.inc()

    data = branches.get()
    download = "https://{0}{1}".format(request.environ['HTTP_HOST'],
                                       url_for('v1.download_php'))
    cached = "https://{0}{1}".format(request.environ['HTTP_HOST'],
                                     '/releases')

    # Override download links
    for branch in data.values():
        branch['direct_url'] = branch['url']
        branch['cached_url'] = '{0}/{1[filename]}'.format(cached, branch)
        branch['url'] = '{0}?branch={1[name]}'.format(download, branch)

    return jsonify(data)


@v1.route("/download.php")
def download_php():
    branch = request.args.get('branch')
    data = branches.get()

    if branch is None or branch not in data.keys():
        abort(404)

    version_key = 'build' if data[branch]['by_build'] == '1' else 'version'
    version = data[branch][version_key]
    metric_download.labels(branch, version).inc()

    url = "/releases/" + data[branch]['filename']
    return render_template('redirect.html', url=url)


@v1.route("/statistics.php", methods=['POST'])
def statistics():
    return ''
