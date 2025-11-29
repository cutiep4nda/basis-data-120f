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

ALTER TABLE event
ADD CONSTRAINT ev_id_tempat_fk
FOREIGN KEY(id_tempat) REFERENCES tempat(id);


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

CREATE TABLE users (
    id_user SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password TEXT NOT NULL
);

INSERT INTO users (username, password)
VALUES ('admin', '$2y$10$1mtlwXXXS5d6rrV2n.gFx.o3NQuhMgaO9wSQwLx7iU5QJEuLNxWXy');




