#!/bin/bash

mysql -uroot <<EOF
delete from demo2.ip_records;
EOF

rm -rf /var/lib/php/sessions/*
