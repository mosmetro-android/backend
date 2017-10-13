#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from .. import branches
from ..util.requests import CachedRequests

import requests

from flask import Blueprint, Response, abort, stream_with_context

releases = Blueprint('releases', __name__)


@releases.route("/<file>")
def download(file):
    matches = [branch
               for branch in branches.get().values()
               if branch['filename'] == file]

    if len(matches) == 0:
        abort(404)
    else:
        branch = matches[0]

    with CachedRequests():
        res = requests.get(branch['url'], stream=True)

    return Response(stream_with_context(res.iter_content(chunk_size=2048)),
                    content_type=res.headers["content-type"])
