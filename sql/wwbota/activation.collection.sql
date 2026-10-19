select distinct
    a.`date`,
    a.`reference`
from
    wwbota_activation as a
order by
    date desc
;