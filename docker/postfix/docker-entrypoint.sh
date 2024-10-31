#!/bin/bash
set -e

# Disable SMTPUTF8 as ICU libraries are missing in Alpine Linux
postconf -e "smtputf8_enable=no"

# Enable long, non-repeating, queue IDs
postconf -e "enable_long_queue_ids=yes"

# Log to stdout
postconf -e "maillog_file=/dev/stdout"

# No limit on mailbox size
postconf -e "mailbox_size_limit=0"

# Set queue lifetime to 0 to attempt mail delivery only once
postconf -e "maximal_queue_lifetime=0"
postconf -e "bounce_queue_lifetime=0"

# Enable alias_maps
echo "mail: /dev/null" >> /etc/postfix/aliases
postalias /etc/postfix/aliases
postconf -e "alias_maps=lmdb:/etc/postfix/aliases"

# Limit message size to 10MB
postconf -e "message_size_limit=10240000"

# Allow requests from localhost and specified IP ranges
postconf -e "mynetworks=127.0.0.0/8,10.0.0.0/8,172.16.0.0/12,192.168.0.0/16"

# Set up hostname and destination
postconf -e "myhostname=postvel"
postconf -e "mydestination=postvel,localhost"

# Enable port 587 (submission)
sed -i -r -e 's/^#submission/submission/' /etc/postfix/master.cf

# Generate and set a self-signed certificate for Postfix
openssl req -new -newkey rsa:2048 -days 365 -nodes -x509 \
    -subj "/C=PL/ST=Masovian/L=Warsaw/O=Postvel/CN=postvel" \
    -keyout /etc/ssl/private/postfix.key \
    -out /etc/ssl/certs/postfix.crt

chmod 600 /etc/ssl/private/postfix.key

postconf -e "smtpd_tls_cert_file=/etc/ssl/certs/postfix.crt"
postconf -e "smtpd_tls_key_file=/etc/ssl/private/postfix.key"
postconf -e "smtp_tls_security_level=may"

# Disable strict mailbox ownership checks
postconf -e "strict_mailbox_ownership=no"

# Forward unknown users and allow dynamic hosts
postconf -e "luser_relay=mail"
postconf -e "local_recipient_maps="
postconf -e "virtual_alias_maps="

# Create header_checks file to ignore specific headers
echo '/^Received: from \[127\.0\.0\.1\]/     IGNORE' > /etc/postfix/header_checks
echo '/^Received: from \[172\./              IGNORE' >> /etc/postfix/header_checks
postconf -e "header_checks=regexp:/etc/postfix/header_checks"

echo
echo 'Postfix configured.'
echo

exec "$@"
