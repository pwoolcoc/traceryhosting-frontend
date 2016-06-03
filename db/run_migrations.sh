#!/bin/bash

set -euo pipefail
IFS=$'\n\t'

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

host=${MYSQL_HOST:-"127.0.0.1"}
port=${MYSQL_PORT:-3306}

for script in $(ls $DIR/migrations/* | sort -n) ; do
    mysql -u $MYSQL_USER -p $MYSQL_PASS -h $host -P $port -d $MYSQL_DB_NAME < $script
done
