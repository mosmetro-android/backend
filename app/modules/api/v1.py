#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .. import branches
from ..branches.github import GitHub

import json
import statsd

from parse import parse
from flask import url_for, Blueprint, render_template, request, abort

v1 = Blueprint('v1', __name__)
stat = statsd.StatsClient('statsd', 8125, prefix='mosmetro')


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
        url = GitHub(modules[module][0], modules[module][1])['play']['url']
        return render_template('redirect.html', url=url)

    if branch is None or branch not in data.keys():
        abort(404)

    if data[branch]['by_build'] == "1":
        stat.incr('update.{0}.{1}'.format(branch, data[branch]['build']))
    else:
        stat.inct('update.{0}.{1}'.format(branch, data[branch]['version']))

    url = "/releases/" + data[branch]['filename']
    return render_template('redirect.html', url=url)


def escape(string):
    if string is None:
        return 'null'

    return string.translate([(x, '-') for x in [',', '.']])


@v1.route("/statistics.php", methods=['GET', 'POST'])
def statistics():
    success = request.form.get('success')
    if success is not None:
        stat.incr('success.' + escape(success))

    version = request.form.get('version')
    if version is not None:
        parsed = parse('{name}-{code:d}', version)
        stat.incr('version.name.' + escape(parsed['name']))
        stat.incr('version.code.' + parsed['code'])

    provider = [request.form.get(p)
                for p in ['p', 'provider']
                if request.form.get(p) is not None]
    if len(provider) > 0:
        stat.incr('provider.' + escape(provider[0]))

    domain = request.environ['HTTP_HOST']
    if domain is not None:
        stat.incr('domain.' + escape(domain))

    captcha = request.form.get('captcha')
    if captcha is not None:
        stat.incr('captcha.' + escape(captcha))

    segment = request.form.get('segment')
    if segment is not None:
        stat.incr('segment.' + escape(segment))

    return json.dumps(dict(status='success'))
