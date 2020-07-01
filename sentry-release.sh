# Assumes you're in a git repository
export SENTRY_AUTH_TOKEN=41a1da6fab85442ca8689e77da4c2a2ab8fcedc9b34548379041327078abc4a5
export SENTRY_ORG=guesthouser
export SENTRY_LOG_LEVEL=debug
# Associate commits with the release
node_modules/.bin/sentry-cli releases new -p "lumin-api"  "api-1.6.3"
node_modules/.bin/sentry-cli releases set-commits --auto "api-1.6.3"
node_modules/.bin/sentry-cli  releases deploys "api-1.6.3" new -e production
