insert into wwbota_award (
    `award`,
    `details`,
    `levels`,
    `name`,
    `scheme`,
    `score`,
    `type`,
    `modified`
) values (
    :award,
    :details,
    :levels,
    :name,
    :scheme,
    :score,
    :type,
    utc_timestamp
) on duplicate key update 
    `score` = :score, 
    `details` = :details, 
    `levels` = :levels, 
    `name` = :name, 
    `modified` = utc_timestamp
;