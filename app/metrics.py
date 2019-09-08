import os

from .util.config import config


try:
    import uwsgi  # throws ImportError if running without uWSGI

    os.environ['prometheus_multiproc_dir'] = '/tmp'

    from prometheus_flask_exporter.multiprocess import UWsgiPrometheusMetrics
    metrics = UWsgiPrometheusMetrics(app=None)
    metrics.start_http_server(int(config['metrics_port']))
except ImportError:
    os.environ['DEBUG_METRICS'] = '1'

    from prometheus_flask_exporter import PrometheusMetrics
    metrics = PrometheusMetrics(app=None)
