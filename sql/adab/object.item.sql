select
    adab.*,
    bota.`reference` as bota
from adab_object as adab
left outer join wwbota_reference as bota on replace(substring_index(bota.`name`, ' ', 1), 'A', 'A0') = adab.`reference`
where adab.`reference` = :reference
limit 1
;