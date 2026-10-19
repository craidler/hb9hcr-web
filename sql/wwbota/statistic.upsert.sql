insert into wwbota_statistic (
    `role`,
    `year`,
    `month`,
    `activations`,
    `activators`,
    `bunkers`,
    `hunters`,
    `modified`
) values ( 
    upper(substring(:role, 1, 1)),
    :year,
    :month,
    :activations,
    :activators,
    :bunkers,
    :hunters,
    utc_timestamp
) on duplicate key update
    `activations` = :activations,
    `activators` = :activators,
    `bunkers` = :bunkers,
    `hunters` = :hunters,
    `modified` = utc_timestamp
;