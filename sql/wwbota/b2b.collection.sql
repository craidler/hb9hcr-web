select distinct
    a.`bunker_to_bunkers` as reference,
    a.`date`
from
    wwbota_activation as a
where
    2 < length(a.`bunker_to_bunkers`)
order by
    date desc
;