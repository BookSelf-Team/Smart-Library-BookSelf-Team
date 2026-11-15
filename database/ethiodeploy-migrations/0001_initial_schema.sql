-- Initial schema for the EthioDeploy Postgres addon.
-- Generated from the Laravel migrations; all migrations are marked as applied.

--
-- PostgreSQL database dump
--


-- Dumped from database version 17.6
-- Dumped by pg_dump version 18.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET search_path = public;
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: public; Type: SCHEMA; Schema: -; Owner: -
--



SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: books; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE books (
    id bigint NOT NULL,
    kode_buku character varying(15) NOT NULL,
    judul character varying(150) NOT NULL,
    pengarang character varying(100) NOT NULL,
    tahun_terbit integer NOT NULL,
    jumlah_stok integer DEFAULT 1 NOT NULL,
    sinopsis text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    cover_image character varying(255)
);


--
-- Name: books_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE books_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: books_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE books_id_seq OWNED BY books.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection character varying(255) NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE failed_jobs_id_seq OWNED BY failed_jobs.id;


--
-- Name: fines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE fines (
    id bigint NOT NULL,
    loan_id bigint NOT NULL,
    nominal integer NOT NULL,
    status_pembayaran character varying(20) DEFAULT 'BELUM'::character varying NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: fines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE fines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: fines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE fines_id_seq OWNED BY fines.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE jobs_id_seq OWNED BY jobs.id;


--
-- Name: loans; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE loans (
    id bigint NOT NULL,
    book_id bigint NOT NULL,
    tanggal_pinjam date DEFAULT CURRENT_DATE NOT NULL,
    batas_kembali date NOT NULL,
    tanggal_dikembalikan date,
    status character varying(30) DEFAULT 'ACTIVE'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    user_id bigint
);


--
-- Name: loans_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE loans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: loans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE loans_id_seq OWNED BY loans.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE migrations_id_seq OWNED BY migrations.id;


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE personal_access_tokens_id_seq OWNED BY personal_access_tokens.id;


--
-- Name: reservations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE reservations (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    book_id bigint NOT NULL,
    queue_position integer DEFAULT 1 NOT NULL,
    status character varying(20) DEFAULT 'WAITING'::character varying NOT NULL,
    reserved_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    ready_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: reservations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE reservations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: reservations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE reservations_id_seq OWNED BY reservations.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE settings (
    key character varying(255) NOT NULL,
    value character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    identifier character varying(255),
    role character varying(255) DEFAULT 'siswa'::character varying NOT NULL,
    email character varying(255),
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    avatar character varying(255)
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: books id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY books ALTER COLUMN id SET DEFAULT nextval('books_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY failed_jobs ALTER COLUMN id SET DEFAULT nextval('failed_jobs_id_seq'::regclass);


--
-- Name: fines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY fines ALTER COLUMN id SET DEFAULT nextval('fines_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY jobs ALTER COLUMN id SET DEFAULT nextval('jobs_id_seq'::regclass);


--
-- Name: loans id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY loans ALTER COLUMN id SET DEFAULT nextval('loans_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY migrations ALTER COLUMN id SET DEFAULT nextval('migrations_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('personal_access_tokens_id_seq'::regclass);


--
-- Name: reservations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY reservations ALTER COLUMN id SET DEFAULT nextval('reservations_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Name: books books_kode_buku_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY books
    ADD CONSTRAINT books_kode_buku_unique UNIQUE (kode_buku);


--
-- Name: books books_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY books
    ADD CONSTRAINT books_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: fines fines_loan_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY fines
    ADD CONSTRAINT fines_loan_id_unique UNIQUE (loan_id);


--
-- Name: fines fines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY fines
    ADD CONSTRAINT fines_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: loans loans_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY loans
    ADD CONSTRAINT loans_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: reservations reservations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY reservations
    ADD CONSTRAINT reservations_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: settings settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (key);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_identifier_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_identifier_unique UNIQUE (identifier);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: books_judul_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX books_judul_index ON books USING btree (judul);


--
-- Name: books_jumlah_stok_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX books_jumlah_stok_index ON books USING btree (jumlah_stok);


--
-- Name: books_pengarang_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX books_pengarang_index ON books USING btree (pengarang);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_expiration_index ON cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_locks_expiration_index ON cache_locks USING btree (expiration);


--
-- Name: failed_jobs_connection_queue_failed_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX failed_jobs_connection_queue_failed_at_index ON failed_jobs USING btree (connection, queue, failed_at);


--
-- Name: fines_status_pembayaran_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX fines_status_pembayaran_index ON fines USING btree (status_pembayaran);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON jobs USING btree (queue);


--
-- Name: loans_book_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX loans_book_id_index ON loans USING btree (book_id);


--
-- Name: loans_status_batas_kembali_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX loans_status_batas_kembali_index ON loans USING btree (status, batas_kembali);


--
-- Name: loans_tanggal_dikembalikan_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX loans_tanggal_dikembalikan_index ON loans USING btree (tanggal_dikembalikan);


--
-- Name: loans_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX loans_user_id_index ON loans USING btree (user_id);


--
-- Name: loans_user_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX loans_user_id_status_index ON loans USING btree (user_id, status);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: reservations_book_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX reservations_book_id_status_index ON reservations USING btree (book_id, status);


--
-- Name: reservations_user_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX reservations_user_id_status_index ON reservations USING btree (user_id, status);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON sessions USING btree (user_id);


--
-- Name: users_role_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX users_role_index ON users USING btree (role);


--
-- Name: fines fines_loan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY fines
    ADD CONSTRAINT fines_loan_id_foreign FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE;


--
-- Name: loans loans_book_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY loans
    ADD CONSTRAINT loans_book_id_foreign FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE;


--
-- Name: loans loans_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY loans
    ADD CONSTRAINT loans_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: reservations reservations_book_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY reservations
    ADD CONSTRAINT reservations_book_id_foreign FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE;


--
-- Name: reservations reservations_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY reservations
    ADD CONSTRAINT reservations_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--



--
-- PostgreSQL database dump
--


-- Dumped from database version 17.6
-- Dumped by pg_dump version 18.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET search_path = public;
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO migrations VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO migrations VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO migrations VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO migrations VALUES (4, '2026_09_30_042054_add_identifier_role_to_users_table', 1);
INSERT INTO migrations VALUES (5, '2026_10_09_174712_add_role_to_users_table', 1);
INSERT INTO migrations VALUES (6, '2026_10_09_190900_create_personal_access_tokens_table', 1);
INSERT INTO migrations VALUES (7, '2026_11_01_022522_create_books_table', 1);
INSERT INTO migrations VALUES (8, '2026_11_01_022523_create_loans_table', 1);
INSERT INTO migrations VALUES (9, '2026_11_01_022524_create_fines_table', 1);
INSERT INTO migrations VALUES (10, '2026_11_01_165250_add_performance_indexes_to_tables', 1);
INSERT INTO migrations VALUES (11, '2026_11_01_165423_add_avatar_to_users_and_cover_image_to_books_tables', 1);
INSERT INTO migrations VALUES (12, '2026_11_01_170943_create_reservations_table', 1);
INSERT INTO migrations VALUES (13, '2026_11_10_035150_create_settings_table', 1);
INSERT INTO migrations VALUES (14, '2026_11_10_035645_make_users_identifier_nullable', 1);
INSERT INTO migrations VALUES (15, '2026_11_15_000000_seed_bookself_admin_account', 1);


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO users VALUES (1, 'Administrator Bookself', 'ADM-000001', 'admin', 'admin@bookself.com', '$2y$12$AQmjfKhZv3WYW5ECEUiQxOogpBD/qsOFz6pzJ0C2SQ8gRu0Fa.cYK', NULL, '2026-11-15 10:07:25', '2026-11-15 10:07:25', NULL);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('migrations_id_seq', 15, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('users_id_seq', 1, true);


--
-- PostgreSQL database dump complete
--


