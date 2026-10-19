insert into wwbota_activation (
    `activator_call`,
    `bands`,
    `modes`,
    `bunker_to_bunkers`,
    `date`,
    `start_time`,
    `end_time`,
    `qso_count`,
    `reference`,
    `modified`
) values (
    :activator_call,
    :bands,
    :modes,
    :bunker_to_bunkers,
    :date,
    :start_time,
    :end_time,
    :qso_count,
    :reference,
    utc_timestamp
) on duplicate key update
    `bunker_to_bunkers` = bunker_to_bunkers,
    `end_time` = :end_time,
    `qso_count` = qso_count,
    `modified` = utc_timestamp;