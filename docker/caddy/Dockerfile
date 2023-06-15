#syntax=docker/dockerfile:1.4

# Prod image
FROM caddy:2.7-builder-alpine AS app_caddy_builder

RUN xcaddy build v2.6.4 \
	--with github.com/dunglas/mercure/caddy \
	--with github.com/dunglas/vulcain/caddy

# Caddy image
FROM caddy:2-alpine AS app_caddy

COPY --from=app_caddy_builder --link /usr/bin/caddy /usr/bin/caddy
COPY ./docker/caddy/Caddyfile /etc/caddy/Caddyfile