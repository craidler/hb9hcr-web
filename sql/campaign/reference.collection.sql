select l.*
from campaign_reference_lookup as l
where l.`adab` regexp :adab
and (coalesce(l.`bota`, '') regexp :bota)
order by l.`adab`, l.`bota`;