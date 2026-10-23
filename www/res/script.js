const marker = {
    locator: L.icon({
        iconUrl: '/image/map/Map.Locator.Pulse.svg',
        iconSize: [50, 50],
        iconAnchor: [25, 35],
        popupAnchor: [50, 25]
    }),
    pointer: L.icon({
        iconUrl: '/image/map/Map.Pointer.svg',
        iconSize: [50, 50],
        iconAnchor: [25, 25],
        popupAnchor: [50, 25]
    }),
    cw: null, // cold war
    to: null, // tank obstacle
    ww: null, // world war
}

const color = {
    complete: '',
    begun: '#2a81cb',
    adab: '#ff6052',
    cw: '#ffd632',
    to: '#207920',
    ww: '#b14137',
}

document.querySelectorAll('.table-paginate').forEach((el) => {
    let pgs = el.querySelectorAll('tbody');

    el.querySelector('nav').addEventListener('click', (e) => {
        e.stopPropagation();
        e.preventDefault();

        if ('A' !== e.target.nodeName) return;

        pgs.forEach((pg) => {
            pg.classList.add('d-none');
        });

        pgs[parseInt(e.target.closest('li').dataset.target)].classList.remove('d-none');
    });
});


document.querySelectorAll('.map-operation').forEach((el) => {
    let map = L.map(el, {preferCanvas:true}).setView([0, 0], 13), 
    data = document.querySelector(el.dataset.source), markers = [], radii = [];
    
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19}).addTo(map);

    data.querySelectorAll('.map-marker').forEach((el) => {
        let d = el.dataset, c = ['#b14137', '#2a81cb', '#7ed217'];

        /*
        radii.push(L.circle([d.lat, d.lon], {
            color: '#32323244',
            radius: 1000,
            weight: 2
        }));
        */

        markers.push(L.circle([d.lat, d.lng], {
            color: c[d.state],
            radius: 10
        }));
    });

    data.querySelectorAll('.map-locator').forEach((el) => {
        let d = el.dataset;
        markers.push(L.marker([d.lat, d.lng], {
            icon: marker.locator
        }));

        radii.push(L.circle([d.lat, d.lng], {
            color: '#32323244',
            radius: 1000,
            weight: 2
        }));
    });

    map.fitBounds(L.featureGroup(markers).addTo(map).getBounds());
    L.featureGroup(radii).addTo(map);
});

document.querySelectorAll('.map-qso').forEach((el) => {
    let map = L.map(el, {preferCanvas:true}).setView([46.87, 8.12], 1), lines = [];

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19}).addTo(map);

    document.querySelectorAll(el.dataset.target + ' .qso-data').forEach(e => {
        lines.push(L.geodesic([
            [parseFloat(e.dataset.lat_a), parseFloat(e.dataset.lon_a)],
            [parseFloat(e.dataset.lat_b), parseFloat(e.dataset.lon_b)],
        ], {
            weight: 3,
            steps: 5,
            color: '#b14137',
        }));
    });

    map.fitBounds(L.featureGroup(lines).addTo(map).getBounds());
});


function trackAndCountByClass(className) {
  const options = {
    root: null,
    threshold: 0.1
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCount(entry.target);
      } else {
        entry.target.innerText = "0";
      }
    });
  }, options);

  const elements = document.querySelectorAll(`.${className}`);
  elements.forEach(element => {
    element.innerText = "0";
    observer.observe(element);
  });
}

function animateCount(element) {
  const target = parseInt(element.getAttribute('data-target') || element.innerText, 10);
  if (isNaN(target)) return;

  const duration = 1000;
  const startTime = performance.now();

  function updateNumber(currentTime) {
    const elapsedTime = currentTime - startTime;
    const progress = Math.min(elapsedTime / duration, 1);
    const currentValue = Math.floor(progress * target);
    element.innerText = currentValue;

    if (progress < 1) {
      requestAnimationFrame(updateNumber);
    }
  }

  requestAnimationFrame(updateNumber);
}

trackAndCountByClass('counter-element');

document.querySelectorAll('.form-file').forEach(el => {
    el.addEventListener('change', e => {
        el.querySelector('span').innerHTML = e.target.value.split('\\').pop();
    });
});


function map(el) {
    let s = document.querySelector(el.dataset.source), i = s.dataset.init ? JSON.parse(atob(s.dataset.init)) : {};

    const m = {
        locators: L.featureGroup(),
        objects: L.featureGroup(),
        pointer: null,
        canvas: L.map(el, {preferCanvas:true}).setView([i.lat ?? 46.81, i.lng ?? 8.2], i.zoom ?? 8),
        source: s,
        tile: L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19}),
        info: el.dataset.hasOwnProperty('info') ? document.querySelector(el.dataset.info) : null,
        init: i,
    }
    
    if (m.info) {
        m.info.querySelector('.map-info-close').addEventListener('click', e => {
            if (m.pointer) m.canvas.removeLayer(m.pointer);
            m.info.classList.add('d-none');
        });

        m.canvas.on('click', e => {
            let within = {
                unplanned: [],
                planned: [],
            };

            m.objects.eachLayer(o => {
                if (1000 < e.latlng.distanceTo(o._latlng)) return;
                within[o.data.planned ? 'planned' : 'unplanned'].push(o);
            });

            if (m.pointer) m.canvas.removeLayer(m.pointer);

            m.canvas.addLayer(m.pointer = L.layerGroup([
                L.marker(e.latlng, {icon: marker.pointer}),
                L.circle(e.latlng, {color: '#32323244', radius: 1000, weight: 2}),
            ]));

            m.info.classList.remove('d-none');
            m.info.querySelector('.map-info-coordinates').innerHTML = [e.latlng.lat.toFixed(6), e.latlng.lng.toFixed(6)].join('/');
            for (const [k, v] of Object.entries(within)) {
                let c = m.info.querySelector('.' + k), q = c.querySelector('.map-info-count'), l = c.querySelector('.map-info-list');

                if (0 < v.length) {
                    let i = [];

                    v.forEach(o => {
                        i.push(o.data.adab + ' ' + o.data.bota);
                    });

                    q.innerHTML = v.length;
                    l.innerHTML = i.join('<br>');
                    c.classList.remove('d-none');
                    continue;
                }

                c.classList.add('d-none');
            }

            m.objects.bringToFront();
        });
    }

    m.source.querySelectorAll('[data-locator').forEach(el => {
        let d = JSON.parse(atob(el.dataset.locator));

        L.marker([d.lat, d.lng], {
            icon: marker.locator
        }).addTo(m.canvas);

        L.circle([d.lat, d.lng], {
            color: '#32323244',
            radius: 1000,
            weight: 2
        }).addTo(m.locators);
    });

    m.source.querySelectorAll('[data-object]').forEach(el => {
        let d = JSON.parse(atob(el.dataset.object)), l = [d.lat, d.lng], o = L.circle(l, {
            color: d.color ?? '#2a81cb',
            radius: d.radius ?? 10,
        }).addTo(m.objects);

        if (d.hasOwnProperty('popup')) o.bindPopup(d.popup);
        if (m.info) o.bindTooltip(d.adab + ' ' + d.bota, {offset: [15, 0]});
        o.data = d;
    });

    m.source.querySelectorAll('[data-polyline]').forEach(el => {
        let pts = [], d = JSON.parse(atob(el.dataset.polyline));

        el.querySelectorAll('[data-point]').forEach(p => {
            p = JSON.parse(atob(p.dataset.point));
            pts.push([p.lat, p.lng]);
        });

        L.polyline(pts, {color: d.color, weight: 12, opacity: .4}).addTo(m.canvas);
        L.polyline(pts, {color: d.color, weight: d.weight}).addTo(m.canvas);
    });

    m.tile.addTo(m.canvas);
    m.locators.addTo(m.canvas);
    m.objects.addTo(m.canvas).bringToFront();

    if (m.init.hasOwnProperty('fit')) {
        m.canvas.fitBounds(m[m.init.fit].getBounds());
    }
}

document.querySelectorAll('.map').forEach(el => map(el));
