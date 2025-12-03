CREATE TABLE cabang(
    id serial not null,
    cabang VARCHAR(20) not null,
    CONSTRAINT cab_id_pk PRIMARY KEY(id)
);

CREATE TABLE divisi(
    id serial not null,
    di_name VARCHAR(255) not null,
    CONSTRAINT div_id_pk PRIMARY KEY(id)
);

CREATE TABLE staff(
    id serial not null,
    nama VARCHAR(255) not null,
    tempat_lahir varchar(20),
    tanggal_lahir date,
    mbti char(6),
    instansi varchar(255),
    id_cabang int not null,
    id_divisi int not null,
    CONSTRAINT st_id_pk PRIMARY KEY(id),
    CONSTRAINT st_id_cb_fk FOREIGN KEY(id_cabang) REFERENCES cabang(id),
    CONSTRAINT st_id_div_fk FOREIGN KEY(id_divisi) REFERENCES divisi(id),
    CONSTRAINT chk_usia_min_17 CHECK (tanggal_lahir <= CURRENT_DATE - INTERVAL '17 years')
);

-- tabel event
CREATE TABLE event(
    id serial not null,
    id_tempat int not null,
    tema_event varchar(100) not null,

    CONSTRAINT event_id_pk PRIMARY KEY(id)
);

-- tabel tempat
CREATE TABLE tempat(
    id serial not null,
    CONSTRAINT temp_id_pk PRIMARY KEY(id)
);

CREATE TABLE tempat_umum(
    id_tempat int not null,
    ruang VARCHAR(50) not null,
    CONSTRAINT tu_id_ru_pk PRIMARY KEY(id_tempat, ruang),
    CONSTRAINT tu_id_fk FOREIGN KEY(id_tempat) REFERENCES tempat(id)
);


CREATE TABLE tempat_panti(
    id_tempat int not null,
    nama_panti varchar(100) not null,
    jml_anak int,
    min_usia int CHECK (min_usia > 0),
    max_usia int CHECK (max_usia > 0),
    min_pendidikan varchar(15) CHECK (min_pendidikan IN ('BELUM SEKOLAH', 'TK', 'SD', 'SMP', 'SMA', 'S1', 'S2', 'S3')),
    max_pendidikan varchar(15) CHECK (max_pendidikan IN ('BELUM SEKOLAH', 'TK', 'SD', 'SMP', 'SMA', 'S1', 'S2', 'S3')),

    CONSTRAINT tp_id_nama_pk PRIMARY KEY(id_tempat, nama_panti),
    CONSTRAINT tp_id_fk FOREIGN KEY(id_tempat) REFERENCES tempat(id)
);

CREATE TABLE event_internal(
    id_event int not null,

    CONSTRAINT ei_id_jk_pk PRIMARY KEY(id_event),
    CONSTRAINT ei_id_fk FOREIGN KEY(id_event) REFERENCES event(id)
);

CREATE TABLE event_eksternal(
    id_event int not NULL,
    tanggal_mulai date not null,
    tanggal_selesai date not null,
    deskripsi TEXT,

    CONSTRAINT ee_id_pk PRIMARY KEY(id_event),
    CONSTRAINT ee_id_fk FOREIGN KEY(id_event) REFERENCES event(id)
);

-- tabel partisipasi
CREATE TABLE partisipasi(
    id_staff int not null,
    id_event int not null,

    CONSTRAINT par_st_ev_pk PRIMARY KEY(id_staff, id_event),
    CONSTRAINT par_st_fk FOREIGN KEY(id_staff) REFERENCES staff(id),
    CONSTRAINT par_ev_fk FOREIGN KEY(id_event) REFERENCES event(id)
);

CREATE TABLE donatur(
    id serial not null,
    nama VARCHAR(50),

    CONSTRAINT dtr_id_pk PRIMARY KEY(id)
);

CREATE TABLE donasi(
    id serial not null,
    id_donatur int not null,
    tanggal date,

    CONSTRAINT dns_id_pk PRIMARY KEY(id),
    CONSTRAINT dns_id_dtr_fk FOREIGN KEY(id_donatur) REFERENCES donatur(id)
);

CREATE TABLE donasi_uang(
    id_donasi int not null,
    nominal DECIMAL(15, 2) not null,

    CONSTRAINT du_id_pk PRIMARY KEY(id_donasi),
    CONSTRAINT du_id_fk FOREIGN KEY(id_donasi) REFERENCES donasi(id)
);

CREATE TABLE donasi_barang(
    id_donasi int not null,
    keterangan varchar(255) not null,
    kuantitas int not null,

    CONSTRAINT db_id_pk PRIMARY KEY(id_donasi),
    CONSTRAINT db_id_fk FOREIGN KEY(id_donasi) REFERENCES donasi(id)
);


ALTER TABLE event_internal
ADD COLUMN tanggal date NOT NULL;

ALTER TABLE tempat_panti
DROP CONSTRAINT tp_id_nama_pk;

ALTER TABLE tempat_panti
ADD CONSTRAINT tp_id_pk PRIMARY KEY(id_tempat);

ALTER TABLE tempat_umum
DROP CONSTRAINT tu_id_ru_pk;

ALTER TABLE tempat_umum
ADD CONSTRAINT tu_id_pk PRIMARY KEY(id_tempat);

ALTER TABLE event
ADD COLUMN nama_event VARCHAR(255) not null;

-- select * from tempat;

-- delete from tempat;


ALTER TABLE tempat_umum
DROP CONSTRAINT tu_id_fk;
ALTER TABLE tempat_umum
add CONSTRAINT tu_id_fk FOREIGN KEY(id_tempat) REFERENCES tempat(id) ON DELETE CASCADE;

ALTER TABLE tempat_panti
DROP CONSTRAINT tp_id_fk;
ALTER TABLE tempat_panti
add CONSTRAINT tp_id_fk FOREIGN KEY(id_tempat) REFERENCES tempat(id) ON DELETE CASCADE;

ALTER TABLE event_internal
DROP CONSTRAINT ei_id_fk;
ALTER TABLE event_internal
ADD CONSTRAINT ei_id_fk FOREIGN KEY(id_event) REFERENCES event(id) ON DELETE CASCADE;

ALTER TABLE event_eksternal
DROP CONSTRAINT ee_id_fk;
ALTER TABLE event_eksternal
ADD CONSTRAINT ee_id_fk FOREIGN KEY(id_event) REFERENCES event(id) ON DELETE CASCADE;


-- Project Event
-- Public Relations
-- human Capital and General Affair
-- Fundraising
-- Media Creative
-- Badan Pengawas Harian

INSERT INTO divisi (di_name)
VALUES
    ('Project Event'),
    ('Public Relations'),
    ('Human Capital and General Affair'),
    ('Fundraising'),
    ('Media Creative'),
    ('Badan Pengawas Harian');

INSERT INTO cabang (cabang)
VALUES ('Bogor'), ('Bandung'), ('Jakarta');

ALTER TABLE event
ADD CONSTRAINT ev_id_tempat_fk
FOREIGN KEY(id_tempat) REFERENCES tempat(id);

ALTER TABLE event_eksternal
ADD CONSTRAINT cek_tgl
check (tanggal_mulai < tanggal_selesai);

-- select * from partisipasi;

ALTER TABLE tempat_panti
ALTER COLUMN jml_anak SET DEFAULT 1,
ALTER COLUMN min_usia SET DEFAULT 1,
ALTER COLUMN max_usia SET DEFAULT 1,
ALTER COLUMN min_pendidikan SET DEFAULT 'BELUM SEKOLAH',
ALTER COLUMN max_pendidikan SET DEFAULT 'BELUM SEKOLAH';

ALTER TABLE tempat_panti
ALTER COLUMN nama_panti TYPE VARCHAR(100),
alter column nama_panti set not null;


alter table tempat_panti
alter column max_pendidikan TYPE VARCHAR(15),
alter column max_pendidikan SET DEFAULT 'BELUM SEKOLAH',
alter column min_pendidikan TYPE VARCHAR(15),
alter column min_pendidikan SET DEFAULT 'BELUM SEKOLAH';

rollback;


SELECT * FROM pg_stat_activity;

BEGIN;
INSERT INTO event (id_tempat, tema_event, nama_event)
VALUES (8, 'Contoh Event', 'Contoh Event');
ROLLBACK;


select * from tempat_panti;



SELECT table_schema, table_name, column_name, data_type, character_maximum_length
FROM information_schema.columns
WHERE character_maximum_length = 10
  AND table_schema = 'public'
ORDER BY table_name, column_name;

select * from tempat;

ALTER TABLE tempat_panti
ADD CONSTRAINT tp_min_cek 
CHECK (min_pendidikan IN ('BELUM SEKOLAH', 'PAUD', 'SD', 'SMP', 'SMA', 'KULIAH'));

ALTER TABLE tempat_panti
ADD CONSTRAINT tp_max_cek 
CHECK (max_pendidikan IN ('BELUM SEKOLAH', 'PAUD', 'SD', 'SMP', 'SMA', 'KULIAH'));


alter table tempat_panti drop CONSTRAINT tp_min_cek, drop CONSTRAINT tp_max_cek;

alter table tempat_panti drop constraint tempat_panti_max_pendidikan_check, drop CONSTRAINT tempat_panti_min_pendidikan_check;