BEGIN;

ALTER TABLE profiles ADD COLUMN push boolean NOT NULL DEFAULT FALSE;
ALTER TABLE profiles ADD COLUMN pushregtime timestamp(0) WITH TIME ZONE NOT NULL DEFAULT NOW();

COMMIT;