#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .. import branches
from ..branches.github import GitHub
from ..util.config import config
from ..util.stats import increment

import redis
import json

from parse import parse
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
    module = request.args.get('module')
    data = branches.get()

    # TODO: Move to separate file
    modules = {
        'captcha_recognition': ('mosmetro-android',
                                'module-captcha-recognition')
    }

    # TODO: Caching of module artifacts
    if module is not None and module in modules.keys():
        cache = redis.StrictRedis(host=config['redis'])

        if cache.exists(module):
            url = cache.get(module)
        else:
            url = GitHub(modules[module][0], modules[module][1])['play']['url']
            cache.set(module, url, ex=3*60*60)  # 3 hours TTL

        return render_template('redirect.html', url=url)

    if branch is None or branch not in data.keys():
        abort(404)

    version = data[branch]['build' if data[branch]['by_build'] else 'version']
    increment('update.{0}'.format(branch), version)

    url = "/releases/" + data[branch]['filename']
    return render_template('redirect.html', url=url)


@v1.route("/statistics.php", methods=['GET', 'POST'])
def statistics():
    increment('success', request.form.get('success'))
    increment('captcha', request.form.get('captcha'))
    increment('segment', request.form.get('segment'))
    increment('domain', request.environ.get('HTTP_HOST'))

    version = request.form.get('version')
    if version is not None:
        parsed = parse('{name}-{code:d}', version)
        increment('version.name', parsed.get('name'))
        increment('version.code', parsed.get('code'))

    for p in ['p', 'provider']:
        provider = request.form.get(p)
        if provider is not None:
            increment('provider', provider)
            break

    return ''
