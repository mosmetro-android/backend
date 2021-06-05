from peewee import *
from datetime import datetime
from .base import BaseModel


class MetricConnection(BaseModel):
    timestamp = DateTimeField(default=datetime.now)
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


class MetricUpdateCheck(BaseModel):
    timestamp = DateTimeField(default=datetime.now)


class MetricDownload(BaseModel):
    timestamp = DateTimeField(default=datetime.now)
    branch = CharField(max_length=32)
    version = IntegerField()
