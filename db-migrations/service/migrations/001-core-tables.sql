-- categories

create table categories
(
    user_id  character varying(255)    not null,
    category character varying(255)    not null,
    date     date default current_date not null,
    primary key (user_id, category)
);

-- operations

create table operations
(
    id       serial primary key,
    user_id  character varying(255)    not null,
    sum      int,
    category character varying(255)    not null,
    date     date default current_date not null,
    constraint fk_user_id_category
        foreign key (user_id, category)
            references categories (user_id, category)
            on delete cascade
);