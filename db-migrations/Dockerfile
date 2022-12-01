FROM liquibase:4.17
ARG LIQUIBASE_MODE=update
COPY changelog /liquibase/changelog
COPY migrations /liquibase/migrations
COPY --chown=liquibase:liquibase runner/$LIQUIBASE_MODE/run.sh /liquibase/
USER liquibase
RUN chmod +x run.sh
ENTRYPOINT ["./run.sh"]
