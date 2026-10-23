create or replace view wwbota_log_activator_summary as select
    l.`reference`                           as reference,
    count(l.`id`)                           as qso_count,
    max(l.`time`)                           as qso_last,
    sum(if(l.`modes`like '%CW%', 1, 0))     as cw_count,
    sum(if(l.`modes`like '%SSB%', 1, 0))    as ssb_count,
    sum(if(l.`modes`like '%FT8%', 1, 0))    as ft8_count
from wwbota_log_activator as l
group by l.`reference`;