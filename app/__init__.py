from flask import Flask, render_template

from . import api
from .models.base import db


app = Flask(__name__)


api.register(app)


@app.route("/")
def hello():
    return render_template('index.html')


@app.before_request
def _db_connect():
    db.connect()


@app.teardown_request
def _db_close(exc):
    if not db.is_closed():
        db.close()


def main():
    app.run(host='0.0.0.0', debug=True, port=8000)


if __name__ == '__main__':
    main()