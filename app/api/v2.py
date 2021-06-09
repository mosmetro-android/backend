from uuid import UUID
from flask import Blueprint, request
from ..models.metrics import MetricConnection


v2 = Blueprint('v2', __name__)


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

    duration: str = request.form.get('duration')
    if duration is not None and duration.isdigit():
        item.duration = int(duration)

    switch: str = request.form.get('switch')
    if switch:
        item.switch = switch

    segment: str = request.form.get('segment')
    provider_branch: str = request.form.get('branch')
    if segment:
        item.segment = segment

        if provider_branch:
            item.provider_branch = provider_branch

    ssid: str = request.form.get('ssid')
    if ssid and ssid != '<unknown ssid>':
        item.ssid = ssid

    api_level: str = request.form.get('api_level')
    if api_level is not None and api_level.isdigit():
        item.android = int(api_level)

    uuid: str = request.form.get('uuid')
    if uuid is not None:
        item.uuid = UUID(uuid)

    item.save()

    return ''
