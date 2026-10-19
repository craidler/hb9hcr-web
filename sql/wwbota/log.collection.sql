select 
    *
from wwbota_log_activator
where `id` like :id
and  `reference` like :reference
order by `time` desc;