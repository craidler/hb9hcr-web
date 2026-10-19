select distinct
    adif.*, 
    dxcc.latitude as lat, 
    dxcc.longitude as lon 
from adif_log as adif 
left outer join dxcc_lookup as dxcc on dxcc.dxcc_code = adif.dxcc 
where callsign like :callsign
order by qso_at desc;