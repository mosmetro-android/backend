#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .. import branches

import json

from flask import url_for, Blueprint, render_template, request, abort

v1 = Blueprint('v1', __name__)


@v1.route("/branches.php")
def branches_php():
    data = branches.get()
    download = "https://{0}{1}".format(request.environ['HTTP_HOST'],
                                       url_for('v1.download_php'))

    # Override download links
    for branch in data.values():
        branch['url'] = '{0}?branch={1[name]}'.format(download, branch)

    return json.dumps(data)


@v1.route("/download.php")
def download_php():
    branch = request.args.get('branch')
    data = branches.get()

    if branch is None:
        abort(404)

    if branch not in data.keys():
        abort(404)

    url = "/releases/" + data[branch]['filename']
    return render_template('redirect.html', url=url)
