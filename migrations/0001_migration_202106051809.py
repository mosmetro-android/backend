# auto-generated snapshot
from peewee import *
import datetime
import peewee


snapshot = Snapshot()


@snapshot.append
class MetricConnection(peewee.Model):
    timestamp = DateTimeField(default=datetime.datetime.now)
    uuid = BinaryUUIDField(null=True)
    branch = CharField(max_length=32)
    build = IntegerField()
    version = IntegerField()
    android = IntegerField(null=True)
    provider = CharField(max_length=32)
    provider_branch = CharField(max_length=32, null=True)
    success = BooleanField()
    midsession = BooleanField()
    duration = IntegerField(null=True)
    switch = CharField(max_length=32, null=True)
    segment = CharField(max_length=32, null=True)
    ssid = CharField(max_length=64, null=True)
    class Meta:
        table_name = "metric_connection"


@snapshot.append
class MetricDownload(peewee.Model):
    timestamp = DateTimeField(default=datetime.datetime.now)
    branch = CharField(max_length=32)
    version = IntegerField()
    class Meta:
        table_name = "metric_download"


@snapshot.append
class MetricUpdateCheck(peewee.Model):
    timestamp = DateTimeField(default=datetime.datetime.now)
    class Meta:
        table_name = "metric_update_check"


