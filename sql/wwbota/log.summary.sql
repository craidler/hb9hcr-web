create or replace view wwbota_log_activator_summary as select
    l.`reference`,
    count(l.`id`) as qso_count,
    max(l.`time`) as qso_last
from wwbota_log_activator as l
group by l.`reference`;