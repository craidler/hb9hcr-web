insert into adif_log (
    `band`,
    `callsign`,
    `cqz`,
    `dxcc`,
    `freq`,
    `info`,
    `ituz`,
    `mode`,
    `pwr`,
    `qsl`,
    `qso_at`,
    `rst_rcvd`,
    `rst_sent`,
    `sig`,
    `station`
)
values (
    :band,
    :callsign,
    :cqz,
    :dxcc,
    :freq,
    :info,
    :ituz,
    :mode,
    :pwr,
    :qsl,
    :qso_at,
    :rst_rcvd,
    :rst_sent,
    :sig,
    :station
)
on duplicate key update
    `sig` = :sig,
    `info` = :info
;