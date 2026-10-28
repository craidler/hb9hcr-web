select
    cr.`adab`,
    cr.`bota`,
    cr.`name`,
    cr.`type`,  
    cr.`lat`,
    cr.`lng`,
    count(l.`id`) as qso_qty,
    max(l.`time`) as qso_last,
    json_arrayagg(l.`hunter_call`) as qso_hunters,
    sum(if(l.`modes` like '%CW%', 1, 0)) as qso_cw,
    sum(if(l.`modes` like '%SSB%', 1, 0)) as qso_ssb,
    sum(if(l.`modes` like '%FT8%', 1, 0)) as qso_ft8
from campaign_reference as cr
left outer join wwbota_log_activator as l on l.`reference` = cr.`bota`
group by cr.`adab`, cr.`bota`, cr.`name`, cr.`type`, cr.`lat`, cr.`lng`
having (cr.`adab` regexp :adab or cr.`adab` is null)
and coalesce(cr.`bota`, '') regexp :bota
order by cr.`adab`, cr.`bota`;