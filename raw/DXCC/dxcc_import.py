#!/usr/bin/env python3
"""
dxcc_import.py - builds DXCC entity / callsign-prefix tables in MySQL.

Data source: Big CTY (cty.dat) from https://www.country-files.com
  -> entities, prefixes, exact-callsign exceptions, coordinates, CQ/ITU zones
DXCC (ARRL/ADIF) entity numbers are NOT part of cty.dat, so they are mapped
via DXCC_CODES below (keyed by cty.dat primary prefix). Entities without a
mapping get dxcc_code = NULL and are listed at the end of the run.

Usage:
  python3 dxcc_import.py --source cty.dat --sql-out dxcc.sql   (write an importable dump, no DB needed)
  mysql your_db < dxcc.sql

  pip install pymysql                                           (only for direct DB import)
  python3 dxcc_import.py --source /path/to/cty.dat              (or .zip, or http(s) URL)
  python3 dxcc_import.py --source cty.dat --dry-run             (parse only)

Env (Docker friendly): DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS, CTY_SOURCE

Tables are dropped and rebuilt on every run, so it is safe to re-run
whenever a new cty.dat is released.

Lookup example (longest prefix wins, exact callsigns first):
  SELECT * FROM dxcc_lookup
  WHERE (is_exact = 1 AND prefix = 'HB9HCR')
     OR (is_exact = 0 AND 'HB9HCR' LIKE CONCAT(prefix, '%'))
  ORDER BY is_exact DESC, CHAR_LENGTH(prefix) DESC LIMIT 1;
"""
from __future__ import annotations

import argparse
import io
import os
import re
import sys
import textwrap
import urllib.request
import zipfile
from pathlib import Path

# DXCC entity numbers (= ADIF DXCC codes) by cty.dat primary prefix.
# Hand-maintained: verify against the current ARRL DXCC list and extend as needed.
DXCC_CODES: dict[str, int] = {
    # North America / Caribbean / Central America
    'K': 291, 'VE': 1, 'XE': 50, 'KL': 6, 'KH6': 110, 'KP4': 202,
    'KP2': 285, 'KH8': 9, 'KH2': 103, 'KH0': 166, 'KH3': 123,
    'KH4': 174, 'KH9': 297, 'KG4': 105, 'KP1': 182, 'KP5': 43,
    'CO': 70, 'HI': 72, 'HH': 78, '6Y': 82, 'C6': 60, '8P': 62,
    '9Y': 90, 'VP9': 64, 'ZF': 69, 'TI': 308, 'HP': 88, 'TG': 76,
    'HR': 80, 'YS': 74, 'YN': 86, 'V3': 66,
    # South America
    'LU': 100, 'PY': 108, 'CE': 112, 'CX': 144, 'ZP': 132,
    'OA': 136, 'HK': 116, 'YV': 148, 'HC': 120, 'CP': 104,
    # Europe
    'G': 223, 'GM': 279, 'GW': 294, 'GI': 265, 'GD': 114, 'GJ': 122,
    'GU': 106, 'EI': 245, 'F': 227, 'DL': 230, 'HB': 287, 'HB0': 251,
    'OE': 206, 'I': 248, 'IS': 225, 'EA': 281, 'EA6': 21, 'EA8': 29,
    'EA9': 32, 'CT': 272, 'CU': 149, 'CT3': 256, 'PA': 263, 'ON': 209,
    'LX': 254, 'OZ': 221, 'OY': 222, 'OX': 237, 'SM': 284, 'LA': 266,
    'JW': 259, 'JX': 118, 'OH': 224, 'OH0': 5, 'OJ0': 167, 'TF': 242,
    'ES': 52, 'YL': 145, 'LY': 146, 'SP': 269, 'OK': 503, 'OM': 504,
    'HA': 239, 'YO': 275, 'LZ': 212, 'SV': 236, 'SV5': 45, 'SV9': 40,
    'SV/a': 180, '9H': 257, 'ZB': 233, 'C3': 203, '3A': 260, 'T7': 278,
    'HV': 295, '1A': 246, '5B': 215, 'ZC4': 283, 'Z3': 502, 'YU': 296,
    'YT': 296, '9A': 497, 'S5': 499, 'E7': 501, '4O': 514, 'Z6': 522,
    'ZA': 7, 'ER': 179, 'UR': 288, 'EU': 27, 'UA': 54, 'UA2': 126,
    'UA9': 15,
    # Asia / Middle East
    '4L': 75, 'EK': 14, '4J': 18, 'UN': 130, 'TA': 390, 'TA1': 390,
    'OD': 354, 'YK': 384, '4X': 336, 'JY': 342, 'HZ': 378, 'A6': 391,
    'A7': 376, '9K': 348, 'A4': 370, 'A9': 304, '7O': 492, 'EP': 330,
    'YI': 333, 'YA': 3, 'UK': 292, 'EX': 135, 'EY': 262, 'EZ': 280,
    'JT': 363, 'JA': 339, 'BY': 318, 'HL': 137, 'P5': 344, 'BV': 386,
    'VR2': 321, 'XX9': 152, 'VU': 324, 'AP': 372, 'S2': 305, '4S': 315,
    '9N': 369, 'HS': 387, '3W': 293, 'XU': 312, 'XW': 143, '9M2': 299,
    '9M6': 46, '9V': 381, 'YB': 327, 'DU': 375, 'XZ': 309,
    # Oceania
    'VK': 150, 'ZL': 170, '3D2': 176, 'FO': 175, 'FK': 162, '5W': 190,
    'A3': 160, 'P2': 163,
    # Africa
    'SU': 478, 'CN': 446, '7X': 400, '3V': 474, '5A': 436, '5N': 450,
    '5Z': 430, 'ET': 53, '5H': 470, 'ZS': 462, 'V5': 464, 'Z2': 452,
    '9J': 482, 'A2': 402, '5R': 438, '3B8': 165, 'FR': 453, '6W': 456,
    '9G': 424, 'TJ': 406, 'D2': 401, 'C9': 181, '5X': 286, 'ST': 466,
    # Remaining entities (islands, small territories, rest of Africa/Caribbean/Pacific)
    '1S': 247, '3B6': 4, '3B9': 207, '3C': 49, '3C0': 195, '3D2/c': 489, '3D2/r': 460,
    '3DA': 468, '3X': 107, '4U1I': 117, '4U1U': 289, '4W': 511, '5T': 444, '5U': 187,
    '5V': 483, '7P': 432, '7Q': 440, '8Q': 159, '8R': 129, '9L': 458, '9Q': 414,
    '9U': 404, '9X': 454, 'A5': 306, 'BS7': 506, 'BV9P': 505, 'C2': 157, 'C5': 422,
    'CE0X': 217, 'CE0Y': 47, 'CE0Z': 125, 'CM': 70, 'CY0': 211, 'CY9': 252, 'D4': 409,
    'D6': 411, 'E3': 51, 'E4': 510, 'E5/n': 191, 'E5/s': 234, 'E6': 188, 'EL': 434,
    'FG': 79, 'FH': 169, 'FJ': 516, 'FK/c': 512, 'FM': 84, 'FO/a': 508, 'FO/c': 36,
    'FO/m': 509, 'FP': 277, 'FS': 213, 'FT/g': 99, 'FT/j': 124, 'FT/t': 276, 'FT/w': 41,
    'FT/x': 131, 'FT/z': 10, 'FW': 298, 'FY': 63, 'H4': 185, 'H40': 507, 'HC8': 71,
    'HK0/a': 216, 'HK0/m': 161, 'J2': 382, 'J3': 77, 'J5': 109, 'J6': 97, 'J7': 95,
    'J8': 98, 'JD/m': 177, 'JD/o': 192, 'KH1': 20, 'KH5': 197, 'KH7K': 138, 'KH8/s': 515,
    'P4': 91, 'PJ2': 517, 'PJ4': 520, 'PJ5': 519, 'PJ7': 518, 'PY0F': 56, 'PY0S': 253,
    'PY0T': 273, 'PZ': 140, 'R1FJ': 61, 'S0': 302, 'S7': 379, 'S9': 219, 'T2': 282,
    'T30': 301, 'T31': 31, 'T32': 48, 'T33': 490, 'T5': 232, 'T8': 22, 'TI9': 37,
    'TK': 214, 'TL': 408, 'TN': 412, 'TR': 420, 'TT': 410, 'TU': 428, 'TY': 416,
    'TZ': 442, 'V2': 94, 'V4': 249, 'V6': 173, 'V7': 168, 'V8': 345, 'VK0H': 111,
    'VK0M': 153, 'VK9C': 38, 'VK9L': 147, 'VK9M': 171, 'VK9N': 189, 'VK9W': 303,
    'VK9X': 35, 'VP2E': 12, 'VP2M': 96, 'VP2V': 65, 'VP5': 89, 'VP6': 172, 'VP6/d': 513,
    'VP8': 141, 'VP8/g': 235, 'VP8/h': 241, 'VP8/o': 238, 'VP8/s': 240, 'VQ9': 33,
    'VR': 321, 'VU4': 11, 'VU7': 142, 'XF4': 204, 'XT': 480, 'YJ': 158, 'YV0': 17,
    'Z8': 521, 'ZD7': 250, 'ZD8': 205, 'ZD9': 274, 'ZK3': 270, 'ZL7': 34, 'ZL8': 133,
    'ZL9': 16, 'ZS8': 201,
    # Antarctica
    'CE9': 13, '3Y/b': 24, '3Y/p': 199,
}

# ---------------------------------------------------------------- source ---


def load_source(source: str) -> str:
    if re.match(r'^https?://', source, re.I):
        req = urllib.request.Request(source, headers={'User-Agent': 'dxcc-import/1.0'})
        with urllib.request.urlopen(req, timeout=60) as resp:
            data = resp.read()
    else:
        data = Path(source).read_bytes()
    if not data:
        raise RuntimeError(f'Could not read source: {source}')

    # zip archive (country-files.com distributes cty as .zip)
    if data[:2] == b'PK':
        with zipfile.ZipFile(io.BytesIO(data)) as zf:
            for name in zf.namelist():
                if os.path.basename(name).lower() == 'cty.dat':
                    data = zf.read(name)
                    break
            else:
                raise RuntimeError('cty.dat not found inside the zip.')
    return data.decode('utf-8', errors='replace')


# ---------------------------------------------------------------- parser ---

_RE_TOKEN = re.compile(r'^([A-Z0-9/]+)(.*)$', re.I)
_RE_CQ = re.compile(r'\((\d+)\)')
_RE_ITU = re.compile(r'\[(\d+)\]')
_RE_LATLON = re.compile(r'<\s*(-?[\d.]+)\s*/\s*(-?[\d.]+)\s*>')
_RE_CONT = re.compile(r'\{([A-Z]{2})\}')


def parse_alias(tok: str) -> dict | None:
    """Parse one prefix token such as '=HB9HCR/P(14)[28]<47.0/-8.0>{EU}'."""
    tok = tok.strip()
    if not tok:
        return None
    exact = tok.startswith('=')
    if exact:
        tok = tok[1:]
    m = _RE_TOKEN.match(tok)
    if not m:
        return None
    row = {'prefix': m.group(1).upper(), 'exact': exact,
           'cq': None, 'itu': None, 'cont': None, 'lat': None, 'lon': None}
    ov = m.group(2)
    if (x := _RE_CQ.search(ov)):
        row['cq'] = int(x.group(1))
    if (x := _RE_ITU.search(ov)):
        row['itu'] = int(x.group(1))
    if (x := _RE_LATLON.search(ov)):
        row['lat'] = float(x.group(1))
        row['lon'] = -float(x.group(2))        # cty.dat: positive = West
    if (x := _RE_CONT.search(ov)):
        row['cont'] = x.group(1)
    return row


def parse_cty(raw: str) -> list[dict]:
    entities: list[dict] = []
    cur: dict | None = None
    buf = ''

    for line in raw.splitlines():
        if not line.strip():
            continue
        if cur is None:
            f = [p.strip() for p in line.split(':')]
            if len(f) < 8:
                raise RuntimeError(f'Unexpected header line: {line}')
            primary = f[7]
            cur = {
                'name': f[0],
                'cq': int(f[1]),
                'itu': int(f[2]),
                'cont': f[3],
                'lat': float(f[4]),
                'lon': -float(f[5]),           # cty.dat: positive = West
                'utc': -float(f[6]),           # cty.dat: positive = West
                'is_dxcc': not primary.startswith('*'),  # '*' = WAE-only, not DXCC
                'primary': primary.lstrip('*'),
                'aliases': [],
            }
            buf = ''
            continue

        buf += line.strip()
        if buf.endswith(';'):
            for tok in buf.rstrip(';').split(','):
                a = parse_alias(tok)
                if a:
                    cur['aliases'].append(a)
            entities.append(cur)
            cur = None

    if cur is not None:
        raise RuntimeError('Truncated file: last entity has no terminating ";".')
    return entities


# ------------------------------------------------------------------ main ---

DDL = [
    'DROP VIEW IF EXISTS dxcc_lookup',
    'DROP TABLE IF EXISTS dxcc_prefixes',
    'DROP TABLE IF EXISTS dxcc_entities',
    """
    CREATE TABLE dxcc_entities (
        id             SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        dxcc_code      SMALLINT UNSIGNED NULL COMMENT 'ARRL/ADIF DXCC entity number',
        name           VARCHAR(100) NOT NULL,
        primary_prefix VARCHAR(12) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
        is_dxcc        TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0 = WAE-only / non-DXCC entity',
        continent      CHAR(2) NOT NULL,
        cq_zone        TINYINT UNSIGNED NOT NULL,
        itu_zone       TINYINT UNSIGNED NOT NULL,
        latitude       DECIMAL(7,4) NOT NULL COMMENT 'degrees, + = North',
        longitude      DECIMAL(8,4) NOT NULL COMMENT 'degrees, + = East',
        utc_offset     DECIMAL(4,1) NOT NULL COMMENT 'hours, + = East of UTC',
        KEY idx_dxcc_code (dxcc_code),
        KEY idx_primary_prefix (primary_prefix)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    """,
    """
    CREATE TABLE dxcc_prefixes (
        id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        entity_id  SMALLINT UNSIGNED NOT NULL,
        prefix     VARCHAR(24) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
        is_exact   TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = full callsign exception, 0 = prefix',
        cq_zone    TINYINT UNSIGNED NULL COMMENT 'override, NULL = use entity value',
        itu_zone   TINYINT UNSIGNED NULL,
        continent  CHAR(2) NULL,
        latitude   DECIMAL(7,4) NULL,
        longitude  DECIMAL(8,4) NULL,
        KEY idx_prefix (prefix, is_exact),
        KEY idx_entity (entity_id),
        CONSTRAINT fk_prefix_entity FOREIGN KEY (entity_id)
            REFERENCES dxcc_entities (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    """,
    """
    CREATE VIEW dxcc_lookup AS
    SELECT p.id AS prefix_id, e.dxcc_code, e.name AS entity_name, e.primary_prefix,
           p.prefix, p.is_exact, e.is_dxcc,
           COALESCE(p.continent, e.continent) AS continent,
           COALESCE(p.cq_zone,   e.cq_zone)   AS cq_zone,
           COALESCE(p.itu_zone,  e.itu_zone)  AS itu_zone,
           COALESCE(p.latitude,  e.latitude)  AS latitude,
           COALESCE(p.longitude, e.longitude) AS longitude,
           e.utc_offset
    FROM dxcc_prefixes p
    JOIN dxcc_entities e ON e.id = p.entity_id
    """,
]

INS_ENTITY = (
    'INSERT INTO dxcc_entities (id,dxcc_code,name,primary_prefix,is_dxcc,continent,'
    'cq_zone,itu_zone,latitude,longitude,utc_offset) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)'
)
INS_PREFIX = (
    'INSERT INTO dxcc_prefixes (entity_id,prefix,is_exact,cq_zone,itu_zone,continent,'
    'latitude,longitude) VALUES (%s,%s,%s,%s,%s,%s,%s,%s)'
)
ENTITY_COLS = 'id,dxcc_code,name,primary_prefix,is_dxcc,continent,cq_zone,itu_zone,latitude,longitude,utc_offset'
PREFIX_COLS = 'entity_id,prefix,is_exact,cq_zone,itu_zone,continent,latitude,longitude'


def build_rows(entities: list[dict]):
    """Flatten parsed entities into row tuples (explicit entity ids)."""
    ent_rows: list[tuple] = []
    pre_rows: list[tuple] = []
    unmapped: list[str] = []
    for eid, e in enumerate(entities, 1):
        code = DXCC_CODES.get(e['primary']) if e['is_dxcc'] else None
        if e['is_dxcc'] and code is None:
            unmapped.append(f"{e['primary']} ({e['name']})")
        ent_rows.append((eid, code, e['name'], e['primary'], int(e['is_dxcc']), e['cont'],
                         e['cq'], e['itu'], e['lat'], e['lon'], e['utc']))

        # make sure the primary prefix itself is searchable
        aliases = [{'prefix': e['primary'].upper(), 'exact': False, 'cq': None,
                    'itu': None, 'cont': None, 'lat': None, 'lon': None}] + e['aliases']
        seen: set[tuple] = set()
        for a in aliases:
            key = (a['exact'], a['prefix'])
            if key in seen:
                continue
            seen.add(key)
            pre_rows.append((eid, a['prefix'], int(a['exact']), a['cq'], a['itu'],
                             a['cont'], a['lat'], a['lon']))
    return ent_rows, pre_rows, unmapped


def sql_lit(v) -> str:
    if v is None:
        return 'NULL'
    if isinstance(v, float):
        return repr(round(v, 4))
    if isinstance(v, int):
        return str(v)
    return "'" + str(v).replace('\\', '\\\\').replace("'", "''") + "'"


def write_sql(path: str, ent_rows, pre_rows, label: str) -> None:
    def inserts(f, table, cols, rows, chunk=500):
        for i in range(0, len(rows), chunk):
            f.write(f'INSERT INTO {table} ({cols}) VALUES\n')
            f.write(',\n'.join('(' + ','.join(sql_lit(v) for v in r) + ')'
                               for r in rows[i:i + chunk]))
            f.write(';\n')
        f.write('\n')

    with open(path, 'w', encoding='utf-8', newline='\n') as f:
        f.write(f'-- DXCC entities and callsign prefixes ({label})\n')
        f.write('-- Import with: mysql <database> < this_file.sql\n\n')
        f.write('SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS = 0;\n\n')
        for stmt in DDL:
            f.write(textwrap.dedent(stmt).strip() + ';\n\n')
        inserts(f, 'dxcc_entities', ENTITY_COLS, ent_rows)
        inserts(f, 'dxcc_prefixes', PREFIX_COLS, pre_rows)
        f.write('SET FOREIGN_KEY_CHECKS = 1;\n')


def main() -> int:
    ap = argparse.ArgumentParser(description='Import cty.dat into MySQL DXCC tables.')
    ap.add_argument('--source', default=os.getenv('CTY_SOURCE'),
                    help='cty.dat / cty.zip path or http(s) URL')
    ap.add_argument('--sql-out', metavar='FILE', help='write an importable .sql dump instead of connecting to MySQL')
    ap.add_argument('--dry-run', action='store_true', help='parse only')
    args = ap.parse_args()

    source = args.source
    if not source and Path(__file__).with_name('cty.dat').is_file():
        source = str(Path(__file__).with_name('cty.dat'))
    if not source:
        ap.error('--source is required (or set CTY_SOURCE)')

    try:
        raw = load_source(source)
        entities = parse_cty(raw)
    except Exception as e:  # noqa: BLE001
        print(f'ERROR: {e}', file=sys.stderr)
        return 1

    ver = re.search(r'VER\d{8}', raw)
    ent_rows, pre_rows, unmapped = build_rows(entities)
    mapped = sum(1 for r in ent_rows if r[1] is not None)
    print(f'Parsed {len(ent_rows)} entities, {len(pre_rows)} prefix/callsign entries'
          f'{" (cty.dat " + ver.group(0) + ")" if ver else ""}.')
    print(f'DXCC numbers assigned: {mapped}, still missing: {len(unmapped)}')
    if unmapped:
        print('Entities without dxcc_code (add them to DXCC_CODES):')
        print('  ' + '\n  '.join(unmapped))

    if args.dry_run:
        return 0
    if args.sql_out:
        write_sql(args.sql_out, ent_rows, pre_rows, f'cty.dat {ver.group(0) if ver else "unknown version"}')
        print(f'Wrote {args.sql_out}')
        return 0

    import pymysql  # imported late so the other modes work without it

    name = os.getenv('DB_NAME', 'hamradio')
    conn = pymysql.connect(
        host=os.getenv('DB_HOST', '127.0.0.1'),
        port=int(os.getenv('DB_PORT', '3306')),
        user=os.getenv('DB_USER', 'root'),
        password=os.getenv('DB_PASS', ''),
        charset='utf8mb4',
        autocommit=True,
    )
    try:
        with conn.cursor() as cur:
            cur.execute(f'CREATE DATABASE IF NOT EXISTS `{name}` '
                        'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')
            cur.execute(f'USE `{name}`')
            for stmt in DDL:                      # DDL auto-commits anyway
                cur.execute(stmt)

        conn.autocommit(False)
        with conn.cursor() as cur:
            cur.executemany(INS_ENTITY, ent_rows)
            for i in range(0, len(pre_rows), 1000):
                cur.executemany(INS_PREFIX, pre_rows[i:i + 1000])
        conn.commit()
    except Exception as e:  # noqa: BLE001
        conn.rollback()
        print(f'ERROR: {e}', file=sys.stderr)
        return 1
    finally:
        conn.close()

    print(f'Import finished into `{name}`.')
    return 0


if __name__ == '__main__':
    sys.exit(main())
