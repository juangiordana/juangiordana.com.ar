#! /bin/sh

# Application PATH.
APP_PATH="$( cd -P "$( dirname "$0" )/../" && pwd )"

# Application name.
APP_NAME="$( basename "$APP_PATH" )"

rsync \
--archive \
--compress \
--delete \
--exclude='.git' \
--exclude='src' \
--exclude='var/run/' \
--filter=':- .gitignore' \
--rsh='ssh -p 37007' \
--timeout=30 \
--verbose \
${APP_PATH}/ \
juan@giordana.com.ar:${APP_PATH}
