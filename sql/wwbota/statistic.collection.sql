select
    s.*,
    case (:role) when 'A' then `hunters` when 'H' then `activators` end as subjects
from wwbota_statistic as s
where s.`role` = :role
order by `year` desc, `month` desc;