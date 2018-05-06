#! /bin/bash
socat tcp-l:10088,fork exec:./deploy.py
