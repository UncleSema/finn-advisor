-- metrics

create table metrics
(
    type   character varying(255)  not null,
    labels character varying(255)  not null,
    value  int,
    time   timestamp default now() not null
);
