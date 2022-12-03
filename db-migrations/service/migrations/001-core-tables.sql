-- operations

create table operations
(
    id               uuid                   not null primary key,
    user_id          character varying(255) not null,
    sum              double precision       not null,
    description      character varying(255)
);

-- users

create table categories
(
    user_id           character varying(255) not null,
    category          character varying(255) not null,
    primary key (user_id, category)
);

