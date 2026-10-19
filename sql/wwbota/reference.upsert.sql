insert into wwbota_reference (
    `reference`,
    `name`,
    `type`,
    `lat`,
    `lng`,
    `loc`,
    `scheme`,
    `modified`
) values (
    :reference,
    :name,
    :type,
    :lat,
    :lng,
    :loc,
    :scheme,
    utc_timestamp
) on duplicate key update 
    `name` = :name, 
    `type` = :type, 
    `lat` = :lat, 
    `lng` = :lng, 
    `loc` = :loc,
    `scheme` = :scheme,
    `modified` = utc_timestamp
;