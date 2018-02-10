#!/usr/bin/env python
# -*- coding: UTF-8 -*-

import requests


class JenkinsBranch(dict):
    def __init__(self, project, name):
        self.base_url = '{0}/job/{1}'.format(project.base_url, name)
        self.api_url = '{0}/api/json'.format(self.base_url)

        branch = requests.get(self.api_url).json()
        build_api_url = branch['lastSuccessfulBuild']
        build = requests.get(build_api_url['url'] + 'api/json').json()

        artifacts = [x['relativePath'] for x in build['artifacts']]
        artifact = [x for x in artifacts if 'signed' in x][0]

        self['name'] = name
        self['version'] = str(0)
        self['build'] = str(build['number'])
        self['by_build'] = str(1)
        self['url'] = "{0[url]}/artifact/{1}".format(build, artifact)
        self['filename'] = "MosMetro-{0[name]}-b{0[build]}.apk".format(self)

        message = "Сборка {0[name]}-#{0[build]}:\n".format(self)

        changes = []
        if build.get('changeSet'):
            changes += build['changeSet']['items']
        elif build.get('changeSets'):
            for cs in build['changeSets']:
                changes += cs['items']

        if len(changes) == 0:
            message += "¯\_(ツ)_/¯"

        for change in changes:
            message += "\n* {0[msg]}".format(change)

        self['message'] = message


class Jenkins(dict):
    def __init__(self, url, project):
        self.base_url = '{0}/job/{1}'.format(url, project)
        self.api_url = '{0}/api/json'.format(self.base_url)

        json = requests.get(self.api_url).json()

        branches = [branch['name']
                    for branch in json['jobs']
                    if branch['color'] != 'disabled']

        for branch in branches:
            self[branch] = JenkinsBranch(self, branch)


if __name__ == "__main__":
    print(Jenkins('https://jenkins.thedrhax.pw', 'MosMetro-Android'))
