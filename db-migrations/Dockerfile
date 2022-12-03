FROM liquibase/liquibase:4.17
ARG LIQUIBASE_MODE=update
COPY service/changelog /liquibase/changelog
COPY service/migrations /liquibase/migrations
COPY --chown=liquibase:liquibase runner/$LIQUIBASE_MODE/run.sh /liquibase/
USER liquibase
RUN chmod +x run.sh
ENTRYPOINT ["./run.sh"]
