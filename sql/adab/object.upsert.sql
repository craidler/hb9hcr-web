insert into adab_object (
    `reference`,
    `name`,
    `type`,
    `lat`,
    `lng`,
    `modified`
) values (
    :reference,
    :name,
    :type,
    :lat,
    :lng,
    utc_timestamp
) on duplicate key update 
    `name` = :name,
    `type` = :type, 
    `lat` = :lat, 
    `lng` = :lng, 
    `modified` = utc_timestamp
;