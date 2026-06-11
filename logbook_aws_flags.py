import pandas as pd
from sqlalchemy import create_engine, text
from datetime import datetime
import numpy as np

# =========================
# PostgreSQL SOURCE
# =========================
pg_user     = "pklstmkg"
pg_password = "CoE2o26$"
pg_host     = "192.168.15.230"
pg_port     = "5432"
pg_database = "db_awscenter"

# =========================
# MySQL TARGET
# =========================
mysql_user     = "root"
mysql_password = ""
mysql_host     = "localhost"
mysql_port     = "3306"
mysql_database = "db_alertdata"

# =========================
# KONEKSI
# =========================
postgres_engine = create_engine(
    f"postgresql+psycopg2://{pg_user}:{pg_password}@{pg_host}:{pg_port}/{pg_database}"
)

mysql_engine = create_engine(
    f"mysql+pymysql://{mysql_user}:{mysql_password}@{mysql_host}:{mysql_port}/{mysql_database}"
)

# =========================
# QUERY — tanpa ws_50cm
# =========================
query = """
    SELECT
        id_station,
        DATE(tanggal)      AS tanggal,
        MAX(long_station)  AS long_station,
        MAX(latt_station)  AS latt_station,
        MAX(elv_station)   AS elv_station,
        MAX(name_station)  AS name_station,
        MAX(nama_kota)     AS nama_kota,
        MAX(tipe_station)  AS tipe_station,

        -- RR (semua tipe stasiun)
        ROUND(100.0 * COUNT(CASE WHEN rr_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS rr_valid,
        ROUND(100.0 * COUNT(CASE WHEN rr_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS rr_invalid,
        ROUND(100.0 * COUNT(CASE WHEN rr_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS rr_missing,

        -- PP_AIR
        ROUND(100.0 * COUNT(CASE WHEN pp_air_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS pp_air_valid,
        ROUND(100.0 * COUNT(CASE WHEN pp_air_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS pp_air_invalid,
        ROUND(100.0 * COUNT(CASE WHEN pp_air_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS pp_air_missing,

        -- RH_AVG
        ROUND(100.0 * COUNT(CASE WHEN rh_avg_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS rh_avg_valid,
        ROUND(100.0 * COUNT(CASE WHEN rh_avg_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS rh_avg_invalid,
        ROUND(100.0 * COUNT(CASE WHEN rh_avg_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS rh_avg_missing,

        -- SR_AVG
        ROUND(100.0 * COUNT(CASE WHEN sr_avg_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS sr_avg_valid,
        ROUND(100.0 * COUNT(CASE WHEN sr_avg_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS sr_avg_invalid,
        ROUND(100.0 * COUNT(CASE WHEN sr_avg_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS sr_avg_missing,

        -- SR_MAX
        ROUND(100.0 * COUNT(CASE WHEN sr_max_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS sr_max_valid,
        ROUND(100.0 * COUNT(CASE WHEN sr_max_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS sr_max_invalid,
        ROUND(100.0 * COUNT(CASE WHEN sr_max_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS sr_max_missing,

        -- NR
        ROUND(100.0 * COUNT(CASE WHEN nr_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS nr_valid,
        ROUND(100.0 * COUNT(CASE WHEN nr_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS nr_invalid,
        ROUND(100.0 * COUNT(CASE WHEN nr_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS nr_missing,

        -- WD_AVG
        ROUND(100.0 * COUNT(CASE WHEN wd_avg_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS wd_avg_valid,
        ROUND(100.0 * COUNT(CASE WHEN wd_avg_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS wd_avg_invalid,
        ROUND(100.0 * COUNT(CASE WHEN wd_avg_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS wd_avg_missing,

        -- WS_AVG
        ROUND(100.0 * COUNT(CASE WHEN ws_avg_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS ws_avg_valid,
        ROUND(100.0 * COUNT(CASE WHEN ws_avg_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS ws_avg_invalid,
        ROUND(100.0 * COUNT(CASE WHEN ws_avg_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS ws_avg_missing,

        -- WS_MAX
        ROUND(100.0 * COUNT(CASE WHEN ws_max_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS ws_max_valid,
        ROUND(100.0 * COUNT(CASE WHEN ws_max_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS ws_max_invalid,
        ROUND(100.0 * COUNT(CASE WHEN ws_max_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS ws_max_missing,

        -- WL
        ROUND(100.0 * COUNT(CASE WHEN wl_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS wl_valid,
        ROUND(100.0 * COUNT(CASE WHEN wl_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS wl_invalid,
        ROUND(100.0 * COUNT(CASE WHEN wl_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS wl_missing,

        -- TT_AIR_AVG
        ROUND(100.0 * COUNT(CASE WHEN tt_air_avg_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS tt_air_avg_valid,
        ROUND(100.0 * COUNT(CASE WHEN tt_air_avg_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS tt_air_avg_invalid,
        ROUND(100.0 * COUNT(CASE WHEN tt_air_avg_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS tt_air_avg_missing,

        -- TT_AIR_MIN
        ROUND(100.0 * COUNT(CASE WHEN tt_air_min_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS tt_air_min_valid,
        ROUND(100.0 * COUNT(CASE WHEN tt_air_min_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS tt_air_min_invalid,
        ROUND(100.0 * COUNT(CASE WHEN tt_air_min_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS tt_air_min_missing,

        -- TT_AIR_MAX
        ROUND(100.0 * COUNT(CASE WHEN tt_air_max_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS tt_air_max_valid,
        ROUND(100.0 * COUNT(CASE WHEN tt_air_max_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS tt_air_max_invalid,
        ROUND(100.0 * COUNT(CASE WHEN tt_air_max_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS tt_air_max_missing,

        -- TT_SEA
        ROUND(100.0 * COUNT(CASE WHEN tt_sea_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS tt_sea_valid,
        ROUND(100.0 * COUNT(CASE WHEN tt_sea_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS tt_sea_invalid,
        ROUND(100.0 * COUNT(CASE WHEN tt_sea_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS tt_sea_missing,

        -- WL_PAN
        ROUND(100.0 * COUNT(CASE WHEN wl_pan_flag::integer = 0             THEN 1 END) / COUNT(*), 2) AS wl_pan_valid,
        ROUND(100.0 * COUNT(CASE WHEN wl_pan_flag::integer BETWEEN 1 AND 8 THEN 1 END) / COUNT(*), 2) AS wl_pan_invalid,
        ROUND(100.0 * COUNT(CASE WHEN wl_pan_flag::integer = 9             THEN 1 END) / COUNT(*), 2) AS wl_pan_missing

    FROM mv_alertdata
    GROUP BY id_station, DATE(tanggal)
    ORDER BY id_station, DATE(tanggal)
"""

# =========================
# ATURAN NULL PER TIPE STASIUN
#
# ARG  → hanya rr, semua lain NULL
# AAWS → semua kecuali tt_sea & wl_pan (NULL)
# ASRS → hanya sr_avg & sr_max, semua lain NULL
# AWS  → semua parameter (tidak ada yang di-NULL)
# =========================

ARG_NULL_COLUMNS = [
    "pp_air_valid",    "pp_air_invalid",    "pp_air_missing",
    "rh_avg_valid",    "rh_avg_invalid",    "rh_avg_missing",
    "sr_avg_valid",    "sr_avg_invalid",    "sr_avg_missing",
    "sr_max_valid",    "sr_max_invalid",    "sr_max_missing",
    "nr_valid",        "nr_invalid",        "nr_missing",
    "wd_avg_valid",    "wd_avg_invalid",    "wd_avg_missing",
    "ws_avg_valid",    "ws_avg_invalid",    "ws_avg_missing",
    "ws_max_valid",    "ws_max_invalid",    "ws_max_missing",
    "wl_valid",        "wl_invalid",        "wl_missing",
    "tt_air_avg_valid","tt_air_avg_invalid","tt_air_avg_missing",
    "tt_air_min_valid","tt_air_min_invalid","tt_air_min_missing",
    "tt_air_max_valid","tt_air_max_invalid","tt_air_max_missing",
    "tt_sea_valid",    "tt_sea_invalid",    "tt_sea_missing",
    "wl_pan_valid",    "wl_pan_invalid",    "wl_pan_missing",
]

AAWS_NULL_COLUMNS = [
    "tt_sea_valid",    "tt_sea_invalid",    "tt_sea_missing",
    "wl_pan_valid",    "wl_pan_invalid",    "wl_pan_missing",
]

ASRS_NULL_COLUMNS = [
    "rr_valid",        "rr_invalid",        "rr_missing",
    "pp_air_valid",    "pp_air_invalid",    "pp_air_missing",
    "rh_avg_valid",    "rh_avg_invalid",    "rh_avg_missing",
    "nr_valid",        "nr_invalid",        "nr_missing",
    "wd_avg_valid",    "wd_avg_invalid",    "wd_avg_missing",
    "ws_avg_valid",    "ws_avg_invalid",    "ws_avg_missing",
    "ws_max_valid",    "ws_max_invalid",    "ws_max_missing",
    "wl_valid",        "wl_invalid",        "wl_missing",
    "tt_air_avg_valid","tt_air_avg_invalid","tt_air_avg_missing",
    "tt_air_min_valid","tt_air_min_invalid","tt_air_min_missing",
    "tt_air_max_valid","tt_air_max_invalid","tt_air_max_missing",
    "tt_sea_valid",    "tt_sea_invalid",    "tt_sea_missing",
    "wl_pan_valid",    "wl_pan_invalid",    "wl_pan_missing",
]

# =========================
# MAIN
# =========================
print("=" * 65)
print("  Flag QC: PostgreSQL → MySQL")
print("  ARG=rr only | AAWS=semua-tt_sea-wl_pan | ASRS=sr_avg+sr_max | AWS=semua")
print("=" * 65)

# --- 1. Baca dari PostgreSQL ---
print("\n[1/5] Membaca data dari PostgreSQL...")
try:
    df = pd.read_sql(query, postgres_engine)
    print(f"      → {len(df)} baris ditemukan")
    print(f"      → Kolom: {list(df.columns)}")
except Exception as e:
    print(f"      [ERROR] Gagal baca PostgreSQL: {e}")
    exit()

if df.empty:
    print("Tidak ada data. Selesai.")
    exit()

# --- 2. Terapkan aturan NULL per tipe stasiun ---
print("\n[2/5] Menerapkan aturan parameter per tipe stasiun...")

tipe = df["tipe_station"].str.lower()

arg_mask  = tipe == "arg"
aaws_mask = tipe == "aaws"
asrs_mask = tipe == "asrs"
aws_mask  = tipe == "aws"

df.loc[arg_mask,  ARG_NULL_COLUMNS]  = np.nan
df.loc[aaws_mask, AAWS_NULL_COLUMNS] = np.nan
df.loc[asrs_mask, ASRS_NULL_COLUMNS] = np.nan
# AWS → tidak ada yang di-NULL

print(f"      → ARG  : {arg_mask.sum():4d} baris | hanya rr")
print(f"      → AAWS : {aaws_mask.sum():4d} baris | semua kecuali tt_sea & wl_pan")
print(f"      → ASRS : {asrs_mask.sum():4d} baris | hanya sr_avg & sr_max")
print(f"      → AWS  : {aws_mask.sum():4d} baris | semua parameter")

# --- 3. Tambah id otomatis + timestamp ---
print("\n[3/5] Menambahkan id & timestamp...")
now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
df["tanggal"]    = pd.to_datetime(df["tanggal"]).dt.date
df["created_at"] = now
df["updated_at"] = now
df.insert(0, "id", range(1, len(df) + 1))
print(f"      → OK, id 1 s/d {len(df)}")

# --- 4. Simpan ke MySQL (UPSERT: insert baru / update jika sudah ada) ---
print("\n[4/5] Menyimpan ke MySQL (station_flags) dengan UPSERT...")
try:
    # Cek apakah tabel sudah ada
    with mysql_engine.connect() as conn:
        result = conn.execute(text(
            "SELECT COUNT(*) FROM information_schema.tables "
            "WHERE table_schema = :db AND table_name = 'station_flags'"
        ), {"db": mysql_database})
        table_exists = result.scalar() > 0

    if not table_exists:
        # Tabel belum ada → buat baru sekalian isi
        print("      → Tabel belum ada, membuat baru...")
        df.to_sql("station_flags", con=mysql_engine, if_exists="replace", index=False)
        print(f"      → Berhasil insert {len(df)} baris")
    else:
        # Tabel sudah ada → UPSERT satu per satu
        print("      → Tabel sudah ada, menjalankan UPSERT...")

        # Ambil semua kolom kecuali id (id di-generate ulang oleh MySQL)
        cols = [c for c in df.columns if c != "id"]
        col_names   = ", ".join([f"`{c}`" for c in cols])
        placeholders = ", ".join([f":{c}" for c in cols])

        # Kolom yang di-update jika data sudah ada (kecuali id_station, tanggal, created_at)
        update_cols = [c for c in cols if c not in ("id_station", "tanggal", "created_at")]
        update_set  = ", ".join([f"`{c}` = VALUES(`{c}`)" for c in update_cols])

        upsert_sql = text(f"""
            INSERT INTO station_flags ({col_names})
            VALUES ({placeholders})
            ON DUPLICATE KEY UPDATE {update_set}
        """)

        inserted = 0
        updated  = 0
        errors   = 0

        with mysql_engine.begin() as conn:
            for _, row in df.iterrows():
                row_dict = {c: (None if (hasattr(row[c], "__class__") and str(type(row[c])) == "<class \'float\'>") and str(row[c]) == "nan" else row[c]) for c in cols}
                # Konversi NaN ke None agar MySQL terima NULL
                row_dict = {c: (None if pd.isna(v) else v) for c, v in row_dict.items()}
                try:
                    result = conn.execute(upsert_sql, row_dict)
                    # rowcount=1 → insert baru, rowcount=2 → update existing
                    if result.rowcount == 1:
                        inserted += 1
                    else:
                        updated += 1
                except Exception as e:
                    print(f"      [WARN] {row.get('id_station','')} {row.get('tanggal','')}: {e}")
                    errors += 1

        print(f"      → Insert baru : {inserted} baris")
        print(f"      → Update      : {updated} baris")
        if errors:
            print(f"      → Gagal       : {errors} baris")

except Exception as e:
    print(f"      [ERROR] Gagal simpan ke MySQL: {e}")
    exit()

# --- 5. Verifikasi ---
print("\n[5/5] Verifikasi...")
try:
    df_check = pd.read_sql(
        "SELECT tipe_station, COUNT(*) AS total FROM station_flags GROUP BY tipe_station",
        mysql_engine
    )
    for _, row in df_check.iterrows():
        print(f"      → {str(row['tipe_station']):10s} : {row['total']} baris")
except Exception as e:
    print(f"      [ERROR] Gagal verifikasi: {e}")

print("\n" + "=" * 65)
print("  Selesai! Cek tabel station_flags di phpMyAdmin.")
print("=" * 65)