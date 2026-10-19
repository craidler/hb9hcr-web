select
    adab.*,
    st_distance_sphere(st_srid(point(adab.`lat`, adab.`lng`), 4326), st_srid(point(:lat, :lng), 4326)) AS distance,
    act.`qso_count`,
    act.`qso_last`
from adab_object as adab
left outer join wwbota_log_activator_summary as act on act.`reference` = adab.`bota`
where adab.`reference` like :reference
having distance <= :distance
order by adab.`reference`, adab.`bota`
;