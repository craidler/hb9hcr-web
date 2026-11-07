select
    *
from wwbota_award
where `score` > 0
-- union select
--   *
-- from crbota_award
-- where `score` > 0
order by `scheme`, `award`, `type`;