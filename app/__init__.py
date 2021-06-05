from flask import Flask, render_template

from .metrics import metrics
from . import api, models


app = Flask(__name__)


models.init()
api.register(app)
metrics.init_app(app)


@app.route("/")
def hello():
    return render_template('index.html')


def main():
    app.run(host='0.0.0.0', debug=True, port=8000)


if __name__ == '__main__':
    main()