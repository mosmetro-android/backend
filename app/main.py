#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from modules import api

from flask import Flask
app = Flask(__name__)

api.register(app)


@app.route("/")
def hello():
    return '¯\_(ツ)_/¯'


if __name__ == "__main__":
    app.run(host='0.0.0.0', debug=True, port=80)
