#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from ..util.requests import CachedRequests

import requests


class GitHubBranch(dict):
    def __init__(self, repo, name, json):
        self['name'] = name
        self['version'] = json['tag_name']
        self['build'] = str(0)
        self['by_build'] = str(0)
        self['url'] = json['assets'][0]['browser_download_url']
        self['message'] = "{0[name]}:\n{0[body]}".format(json)
        self['filename'] = 'MosMetro-{0[name]}-v{0[version]}.apk'.format(self)


class GitHub(dict):
    def __init__(self, user, repo):
        base_url = "https://api.github.com/repos"
        self.api_url = "{0}/{1}/{2}/releases".format(base_url, user, repo)

        with CachedRequests():
            headers = {'User-Agent': 'Mozilla/4.0 (compatible; MSIE 6.0)'}
            json = requests.get(self.api_url, headers=headers).json()

        # Looking for 'beta' (latest) and 'play' (latest stable)
        beta = json[0]
        play = [release for release in json if not release['prerelease']][0]

        self['beta'] = GitHubBranch(self, 'beta', beta)
        self['play'] = GitHubBranch(self, 'play', play)


if __name__ == '__main__':
    print(GitHub('mosmetro-android', 'mosmetro-android'))