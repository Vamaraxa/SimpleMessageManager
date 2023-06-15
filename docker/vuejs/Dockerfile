# syntax=docker/dockerfile:1.4
FROM --platform=$BUILDPLATFORM node:16-alpine AS development

RUN mkdir /srv/vuejs

WORKDIR /srv/vuejs

COPY ./vuejs/ ./

RUN yarn global add @vue/cli
RUN yarn install
ENV HOST=0.0.0.0
CMD ["yarn", "run", "serve"]

FROM development as dev-envs
RUN
RUN apk update
RUN apk add git

RUN
RUN addgroup -S docker
RUN adduser -S --shell /bin/bash --ingroup docker vscode

# install Docker tools (cli, buildx, compose)
COPY --from=gloursdocker/docker / /
CMD ["yarn", "run", "serve"]