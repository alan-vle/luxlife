FROM alpine:3.14

RUN apk --no-cache add tzdata \
    && rm -rf /var/cache/apk/* \

ENV TZ=Europe/Paris
COPY ./docker/cron/crontabs /etc/crontabs/root

COPY ./docker/cron/cron_scripts/problem-car-generator.sh /problem-car-generator

RUN chmod +x /problem-car-generator

COPY docker/cron/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint
ENTRYPOINT ["docker-entrypoint"]