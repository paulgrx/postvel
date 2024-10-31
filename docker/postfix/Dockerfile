FROM alpine:3.20.3

# Install dependencies
RUN apk add --no-cache --update postfix ca-certificates bash openssl && \
    apk add --no-cache --upgrade musl musl-utils && \
    (rm "/tmp/"* 2>/dev/null || true) && (rm -rf /var/cache/apk/* 2>/dev/null || true)

# Mark used folders
VOLUME [ "/var/spool/postfix", "/etc/postfix" ]

# Expose mail submission agent port
EXPOSE 587

# Configure Postfix and logrotate on startup
COPY docker-entrypoint.sh /usr/local/bin/

ENTRYPOINT ["docker-entrypoint.sh"]

# Start postfix in foreground mode
CMD ["postfix", "start-fg"]
