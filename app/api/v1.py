#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .. import branches
from ..models.metrics import MetricDownload, MetricUpdateCheck

from flask import url_for, Blueprint, render_template, request, abort, jsonify


v1 = Blueprint('v1', __name__)


@v1.route("/branches.php")
def branches_php():
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

    try:
        MetricUpdateCheck().save()
    except Exception:
        pass

    return jsonify(data)


@v1.route("/download.php")
def download_php():
    branch = request.args.get('branch')
    data = branches.get()

    if branch is None or branch not in data.keys():
        abort(404)

    version_key = 'build' if data[branch]['by_build'] == '1' else 'version'
    version = data[branch][version_key]

    try:
        MetricDownload(branch=branch, version=version).save()
    except Exception:
        pass

    url = "/releases/" + data[branch]['filename']
    return render_template('redirect.html', url=url)


@v1.route("/statistics.php", methods=['POST'])
def statistics():
    return ''
