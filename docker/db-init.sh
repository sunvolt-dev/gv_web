#!/bin/sh
# MySQL 컨테이너 최초 부팅 시 자동 실행됨.
# sql/schema.sql 에서 'CREATE DATABASE' / 'USE' 구문을 제거한 뒤,
# MYSQL_DATABASE env 가 지정한 DB로 piping → 멀티사이트 안전.
#
# (schema.sql 원본은 Laragon HeidiSQL 사용자가 그대로 import 할 수 있도록 보존)

set -e
echo "[db-init] importing schema into '$MYSQL_DATABASE'..."

awk '
  BEGIN { skip = 0 }
  /^[[:space:]]*(CREATE DATABASE|USE)[[:space:]]/ { skip = 1 }
  skip { if ($0 ~ /;/) { skip = 0 }; next }
  { print }
' /db-init/schema.sql \
  | mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"

echo "[db-init] done."
