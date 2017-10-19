#!/usr/bin/env bash

if [ $# -eq 0 ]; then
    echo "Usage: $0 dev|prod [stat]"
fi

case $1 in
    dev|development)
        export COMPOSE_FILE="compose/1-common.yml:compose/2-development.yml" ;;

    prod|production)
        export COMPOSE_FILE="compose/1-common.yml:compose/2-production.yml" ;;
esac

shift
for arg in $@; do
    case $arg in
        stat|statistics)
            export COMPOSE_FILE="$COMPOSE_FILE:compose/3-statistics.yml" ;;
    esac
done
