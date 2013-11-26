#!/bin/bash

openssl req -x509 -newkey rsa:2048 -keyout crypt.key -out crypt.crt -days 365 -nodes
