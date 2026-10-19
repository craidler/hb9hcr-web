insert into wwbota_log_activator (
    `id`,
    `activator_call`,
    `activator_logged`,
    `band`,
    `hunter_call`,
    `hunter_logged`,
    `modes`,
    `reference`,
    `detail`,
    `rejected`,
    `rst_rcvd`,
    `rst_sent`,
    `time`,
    `modified`
) values (
    :id,
    :activator_call,
    :activator_logged,
    :band,
    :hunter_call,
    :hunter_logged,
    :modes,
    :reference,
    :detail,
    :rejected,
    :rst_rcvd,
    :rst_sent,
    :time,
    utc_timestamp
) on duplicate key update 
    `activator_logged` = :activator_logged, 
    `hunter_logged` = :hunter_logged, 
    `rejected` = :rejected, 
    `modified` = utc_timestamp
;