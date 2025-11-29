
-- 1. Memelihara (enter, update, and delete) data cabang.
-- Tambah data cabang baru
INSERT INTO cabang (cabang) VALUES ('Semarang');
-- Ubah data cabang berdasarkan ID
UPDATE cabang SET cabang = 'Bandung Barat' WHERE id = 2;
-- Hapus data cabang berdasarkan ID
DELETE FROM cabang WHERE id = 6;

-- 2. Memelihara (enter, update, and delete) data tempat panti asuhan mitra.
-- Tambah data tempat baru untuk panti
INSERT INTO tempat DEFAULT VALUES RETURNING id;
INSERT INTO tempat_panti (id_tempat, nama_panti, jml_anak, min_usia, max_usia, min_pendidikan, max_pendidikan)
VALUES (13, 'Panti Asuhan Baru', 50, 4, 17, 'TK', 'SMP');
UPDATE tempat_panti SET jml_anak = 110 WHERE nama_panti = 'Panti Sejahtera';
DELETE FROM tempat_panti WHERE nama_panti = 'Panti Asuhan Baru';
DELETE FROM tempat WHERE id = 13;

INSERT INTO tempat DEFAULT VALUES; 
INSERT INTO tempat_panti (id_tempat, nama_panti, jml_anak, min_usia, max_usia, min_pendidikan, max_pendidikan) VALUES (lastval(), 'Panti Asuhan Baru', 50, 4, 17, 'TK', 'SMP'); 
UPDATE tempat_panti SET jml_anak = 110 WHERE nama_panti = 'Panti Sejahtera'; DELETE FROM tempat_panti WHERE nama_panti = 'Panti Asuhan Baru'; 
DELETE FROM tempat WHERE id = (SELECT id_tempat FROM tempat_panti WHERE nama_panti = 'Panti Asuhan Baru');

-- 3. Memelihara (enter, update, and delete) data staff.
-- Tambah data staff baru
INSERT INTO staff (nama, tempat_lahir, tanggal_lahir, mbti, instansi, id_cabang, id_divisi)
VALUES ('Budi Santoso', 'Medan', '2005-01-01', 'INTJ', 'UI', 3, 2);
-- Ubah data staff berdasarkan nama
UPDATE staff SET mbti = 'ENFJ' WHERE nama = 'Budi Santoso';
-- Hapus data staff berdasarkan nama
DELETE FROM staff WHERE nama = 'Budi Santoso';

-- 4. Memelihara (enter, update, and delete) data event.
-- Tambah data event baru
INSERT INTO tempat DEFAULT VALUES RETURNING id;
-- Misalkan id baru adalah 14
INSERT INTO event (id_tempat, tema_event) VALUES (14, 'Workshop Kepemimpinan');
INSERT INTO event_eksternal (id_event, tanggal_mulai, tanggal_selesai, deskripsi)
VALUES (20, '2025-12-20', '2025-12-20', 'Workshop kepemimpinan untuk anak muda');
-- Ubah tema event
UPDATE event SET tema_event = 'Workshop Kepemimpinan dan Softskill' WHERE id = 20;
-- Hapus event dan data terkait
DELETE FROM partisipasi WHERE id_event = 20;
DELETE FROM event_eksternal WHERE id_event = 20;
DELETE FROM event WHERE id = 20;

-- 5. Memelihara (enter, update, and delete) data donasi.
-- Tambah data donasi baru
INSERT INTO donasi (id_donatur, tanggal) VALUES (1, '2025-11-19');
INSERT INTO donasi_barang (id_donasi, keterangan, kuantitas) VALUES (lastval(), 'Perlengkapan Sekolah', 150);
-- Update data donasi
UPDATE donasi_barang SET kuantitas = 200 WHERE id_donasi = lastval();
--Hapus data donasi
DELETE FROM donasi_barang WHERE id_donasi = lastval();
DELETE FROM donasi WHERE id = lastval();

-- 6. Memelihara (enter, update, and delete) data donatur.
-- Tambah data donatur baru
INSERT INTO donatur (nama) VALUES ('PT. ABC Sejahtera');
-- Ubah nama donatur
UPDATE donatur SET nama = 'PT. ABC Sejahtera Abadi' WHERE id = 9;
-- Hapus data donatur
DELETE FROM donatur WHERE id = 9;

-- 7. Memelihara (enter, update, and delete) data divisi.
-- Tambah data divisi baru
INSERT INTO divisi (di_name) VALUES ('Marketing');
-- Ubah nama divisi
UPDATE divisi SET di_name = 'Partnership and Marketing' WHERE id = 7;
-- Hapus data divisi
DELETE FROM divisi WHERE id = 7;

-- 8. Melakukan pencarian berdasarkan nama panti.
SELECT tp.nama_panti, t.id
FROM tempat_panti tp
JOIN tempat t ON tp.id_tempat = t.id
WHERE tp.nama_panti ILIKE '%Harapan%';

-- 9. Melakukan pencarian staff berdasarkan nama, instansi asal, mbti, atau divisi.
SELECT s.nama, s.instansi, s.mbti, d.di_name
FROM staff s
JOIN divisi d ON s.id_divisi = d.id
WHERE s.nama ILIKE '%Rizky%';


-- 10. Melakukan pencarian event berdasarkan nama, atau tanggal pelaksanaan, tema, tempat, deskripsi.
SELECT e.id, e.tema_event, ee.tanggal_mulai, ee.tanggal_selesai, ee.deskripsi, tu.ruang
FROM event e
LEFT JOIN event_eksternal ee ON e.id = ee.id_event
LEFT JOIN tempat t ON e.id_tempat = t.id
LEFT JOIN tempat_umum tu ON t.id = tu.id_tempat
WHERE e.tema_event ILIKE '%Workshop%';

-- 11. Melakukan pencatatan demografi anak panti asuhan berdasarkan rentang umur dan jenjang pendidikan.
SELECT nama_panti, min_usia, max_usia, min_pendidikan, max_pendidikan, jml_anak
FROM tempat_panti
WHERE nama_panti IS NOT NULL;

-- 12. Melakukan pencarian donasi berdasarkan sumber donatur.
SELECT d.id, dt.nama AS donatur, d.tanggal, du.nominal, db.keterangan, db.kuantitas
FROM donasi d
JOIN donatur dt ON d.id_donatur = dt.id
LEFT JOIN donasi_uang du ON d.id = du.id_donasi
LEFT JOIN donasi_barang db ON d.id = db.id_donasi
WHERE dt.nama ILIKE '%Yayasan%';

-- 13. Melacak keterlibatan staff pada setiap event.
SELECT s.nama, e.tema_event, e.id AS id_event
FROM staff s
JOIN partisipasi p ON s.id = p.id_staff
JOIN event e ON p.id_event = e.id
ORDER BY s.nama;

-- 14. Melaporkan keterlibatan staf per program.
SELECT e.tema_event, COUNT(p.id_staff) AS jumlah_partisipan
FROM event e
JOIN partisipasi p ON e.id = p.id_event
GROUP BY e.id, e.tema_event;

-- 15. Melaporkan donasi berdasarkan donatur.
SELECT 
	dt.nama AS donatur,
	COUNT(d.id) AS jumlah_donasi,
	SUM(du.nominal) AS total_uang, 
	SUM(db.kuantitas) AS total_barang,
	MAX(d.tanggal) as tanggal_donasi_terakhir
FROM donatur dt
JOIN donasi d ON dt.id = d.id_donatur
LEFT JOIN donasi_uang du ON d.id = du.id_donasi
LEFT JOIN donasi_barang db ON d.id = db.id_donasi
GROUP BY dt.id, dt.nama;
