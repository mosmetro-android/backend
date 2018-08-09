#!/usr/bin/env python
# -*- coding: UTF-8 -*-

import requests
from ..util.config import config


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

        headers = {'User-Agent': 'Mozilla/4.0 (compatible; MSIE 6.0)'}
        json = requests.get(self.api_url, headers=headers).json()

        # Looking for 'beta' (latest) and 'play' (latest stable)
        beta = json[0]
        play = [release for release in json if not release['prerelease']][0]

        if config['github']['beta']:
            self['beta'] = GitHubBranch(self, 'beta', beta)
        self['play'] = GitHubBranch(self, 'play', play)


if __name__ == '__main__':
    print(GitHub(config['github']['user'], config['github']['repo']))
