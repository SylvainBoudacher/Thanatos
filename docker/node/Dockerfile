FROM node:17-alpine

RUN apk update
RUN apk add \
    python3 \
    python3-dev \
    py3-pip \
    build-base \
    git \
    openssh-client \
    optipng \
    autoconf \
    jpeg-dev \
    zlib-dev \
    automake \
    libtool \
    pkgconfig \
    rsync \
  && pip install virtualenv \
  && rm -rf /var/cache/apk/*

WORKDIR /app
COPY ./ /app

RUN npm install -g gulp foundation-cli

RUN npm install -D tailwindcss postcss-loader purgecss-webpack-plugin glob-all path autoprefixer

RUN npm set progress=false

RUN npm install

EXPOSE 8080