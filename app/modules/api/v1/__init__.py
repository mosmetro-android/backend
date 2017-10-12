#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from ... import branches

import json

from flask import url_for, Blueprint
v1 = Blueprint('v1', __name__)


@v1.route("/branches.php")
def branches_php():
    data = branches.get()
    download = url_for('v1.download_php')

    # Override download links
    for branch in data.values():
        branch['url'] = '{0}?branch={1[name]}'.format(download, branch)

    return json.dumps(data)


@v1.route("/download.php")
def download_php():
    return ''
