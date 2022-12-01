#!/bin/bash
/liquibase/docker-entrypoint.sh --defaultsFile=changelog/liquibase.properties --url=${DATABASE_URL} --username=${DATABASE_USER} --password=${DATABASE_PASSWORD} --defaultSchemaName=${DATABASE_SCHEMA} update
