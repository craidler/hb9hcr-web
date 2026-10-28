drop table if exists campaign_reference;

create table if not exists campaign_reference as select
    ar.reference as adab,
    br.reference as bota,
    ar.name,
    ar.type,
    ar.lat,
    ar.lng
from adab_reference ar
left join wwbota_reference br on (ar.`reference` = concat('A0', substring(br.`name`, 2, 4)) and br.`name` like 'A%') or (ar.`reference` = substring(br.`name`, 1, 6) and br.`name` like 'F%')
having bota is null
union select
    ar.`reference` as adab,
    br.`reference` as bota,
    br.`name`,
    br.`type`,
    br.`lat`,
    br.`lng`
from wwbota_reference br
left join adab_reference ar on (ar.`reference` = concat('A0', substring(br.`name`, 2, 4)) and br.`name` like 'A%') or (ar.`reference` = substring(br.`name`, 1, 6) and br.`name` like 'F%')
;

ALTER TABLE `campaign_reference` ADD UNIQUE(`adab`, `bota`);
ALTER TABLE `campaign_reference` RENAME INDEX `adab` TO `REFERENCE`;