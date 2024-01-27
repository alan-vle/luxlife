#!/bin/sh
chown root:root /etc/crontabs/root && chmod 600 /etc/crontabs/root
echo "" > /var/log/cron.logs