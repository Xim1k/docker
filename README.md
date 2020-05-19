##Docker \
###First Part \
In doctrine.yaml you should select "server_version: '5.7'" for mysql 5.7 \
make build - is equal to "docker-compose build" \
make up - starting docker-compose \
make down - stopping docker-compose  \
make install - building and starting project  \
make migrate - migrating database and load fixtures \
###Second Part
In doctrine.yaml you should select "server_version: '5.5'" for mysql 5.5 \
make build-prod - is equal to "docker-compose -f docker-compose.prod.yml build" \
make up-prod - starting docker-compose \
make down-prod - stopping docker-compose  \
make install-prod - building and starting project \
make migrate-prod - migrating database and load fixtures
