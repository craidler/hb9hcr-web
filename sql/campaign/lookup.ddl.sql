create or replace view campaign_reference_lookup as select
    a.`reference` as adab,
    a.`bota` as bota,
    a.`name`,
    a.`lat`,
    a.`lng`,
    count(l.`id`) as qso_qty,
    max(l.`time`) as qso_last,
    json_arrayagg(l.`hunter_call`) as qso_hunters,
    sum(if(l.`modes` like '%CW%', 1, 0)) as qso_cw,
    sum(if(l.`modes` like '%SSB%', 1, 0)) as qso_ssb,
    sum(if(l.`modes` like '%FT8%', 1, 0)) as qso_ft8
from adab_object as a
left outer join wwbota_log_activator as l on l.`reference` = a.`bota`
group by a.`reference`, a.`bota`, a.`name`, a.`lat`, a.`lng`
order by a.`reference`, a.`bota`;