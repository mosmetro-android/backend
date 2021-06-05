from uuid import UUID
from flask import Blueprint, request
from prometheus_client import Counter, Gauge
from ..models.metrics import MetricConnection


v2 = Blueprint('v2', __name__)


metric_connect = Counter(
    'mosmetro_connection',
    'Total number of connections',
    ['branch', 'version', 'success'])

metric_ssid = Counter(
    'mosmetro_ssid',
    'Number of connections per SSID',
    ['provider', 'ssid'])

metric_provider = Counter(
    'mosmetro_provider',
    'Number of successful connections per provider',
    ['branch', 'version', 'provider'])

metric_duration = Gauge(
    'mosmetro_duration',
    'Provider execution time in milliseconds',
    ['branch', 'version', 'provider'])

metric_sdk = Counter(
    'mosmetro_sdk',
    'Number of connections per Android SDK level',
    ['level'])

metric_mmv2_segment = Counter(
    'mosmetro_v2_segment',
    'Number of successful connections per MosMetroV2 segment',
    ['branch', 'version', 'segment'])

metric_mmv2_branch = Counter(
    'mosmetro_v2_branch',
    'Association between V2 segments and branches',
    ['segment', 'branch'])

metric_switch = Counter(
    'mosmetro_provider_switch',
    'Number of switches from one provider to another',
    ['from', 'to'])


@v2.route("/stats", methods=['POST'])
def statistics():
    branch: str = request.form.get('build_branch')
    build: int = int(request.form.get('build_number'))
    version: int = int(request.form.get('version_code'))
    provider: str = request.form.get('provider')
    result: str = request.form.get('success')

    item = MetricConnection(branch=branch,
                      build=build,
                      version=version,
                      provider=provider,
                      success=result != 'false',
                      midsession=result == 'midsession')

    metric_connect.labels(branch, version, result).inc()

    if result not in ['true', 'midsession']:
        metric_provider.labels(branch, version, provider).inc()

    duration: str = request.form.get('duration')
    if duration is not None and duration.isdigit():
        item.duration = int(duration)
        if result not in ['true', 'midsession']:
            metric_duration.labels(branch, version, provider).set(int(duration))

    switch: str = request.form.get('switch')
    if switch:
        item.switch = switch
        if result not in ['true', 'midsession']:
            metric_switch.labels(provider, switch).inc()

    segment: str = request.form.get('segment')
    provider_branch: str = request.form.get('branch')
    if segment:
        item.segment = segment
        if result not in ['true', 'midsession']:
            metric_mmv2_segment.labels(branch, version, segment).inc()

        if provider_branch:
            item.provider_branch = provider_branch
            if result not in ['true', 'midsession']:
                metric_mmv2_branch.labels(segment, provider_branch).inc()

    ssid: str = request.form.get('ssid')
    if ssid and ssid != '<unknown ssid>':
        item.ssid = ssid
        if result not in ['true', 'midsession']:
            metric_ssid.labels(provider, ssid).inc()

    api_level: str = request.form.get('api_level')
    if api_level is not None and api_level.isdigit():
        item.android = int(api_level)
        if result not in ['true', 'midsession']:
            metric_sdk.labels(int(api_level)).inc()

    uuid: str = request.form.get('uuid')
    if uuid is not None:
        item.uuid = UUID(uuid)

    item.save()

    return ''
