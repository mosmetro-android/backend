from peewee import *
from playhouse.db_url import connect
from ..util.config import config


db = connect(config['sql'])


class BaseModel(Model):
    class Meta:
        database = db
        legacy_table_names = False
