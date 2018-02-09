FROM tutum/apache-php

ENV ALLOW_OVERRIDE="true"

ADD ./install.sh /install.sh

RUN apt-get update && apt-get install -yq git && rm -rf /var/lib/apt/lists/*
RUN /usr/local/bin/composer self-update

RUN rm -rf /app
