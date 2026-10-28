<?php

namespace HB9HCR;

return [
    'api' => (object)[
        'base' => 'https://api.wwbota.net/',
        'username' => 'HB9HCR',
        'password' => 'p!5dGQXU6eB42ag',
    ],
    'awards' => (object)[
        'wwbota.activator.bunkerfest26' => ['WWBOTA', 'Activator', 'BF 2026'],
        'wwbota.hunter.bunkerfest26' => ['WWBOTA', 'Hunter', 'BF 2026'],
        'wwbota.activator.lifetime' => ['WWBOTA', 'Activator', 'Lifetime'],
        'wwbota.hunter.lifetime' => ['WWBOTA', 'Hunter', 'Lifetime'],
        'wwbota.activator_cw.lifetime' => ['WWBOTA', 'Activator', 'Lifetime CW'],
        'wwbota.hunter_cw.lifetime' => ['WWBOTA', 'Hunter', 'Lifetime CW'],
        'wwbota.activator.annual26' => ['WWBOTA', 'Activator', '2026'],
        'wwbota.activator_dxcc.annual26' => ['WWBOTA', 'Activator', '2026 DXCC'],
        'wwbota.activator_dxcc_b2b.annual26' => ['WWBOTA', 'Activator', '2026 DXCC B2B'],
        'wwbota.hunter.annual26' => ['WWBOTA', 'Hunter', '2026'],
        'wwbota.hunter_dxcc.annual26' => ['WWBOTA', 'Hunter', '2026 DXCC'],
        'hbbota.activator.lifetime' => ['HBBOTA', 'Activator', 'Lifetime'],
        'hbbota.activator_b2b.lifetime' => ['HBBOTA', 'Activator', 'Lifetime B2B'],
        'hbbota.hunter.lifetime' => ['HBBOTA', 'Hunter', 'Lifetime'],
        'hbbota.activator.national26' => ['HBBOTA', 'Activator', 'National 2026'],
        'hbbota.hunter.national26' => ['HBBOTA', 'Hunter', 'National 2026'],
        'pabota.hunter.lifetime' => ['PABOTA', 'Hunter', 'Lifetime'],
        'ukbota.hunter.lifetime' => ['UKBOTA', 'Hunter', 'Lifetime'],
        'ukbota.hunter.annual26' => ['UKBOTA', 'Hunter', '2026'],
        'ukbota.hunter_jul.weekend26' => ['UKBOTA', 'Hunter', '07.2026'],
        'ukbota.hunter_aug.weekend26' => ['UKBOTA', 'Hunter', '08.2026'],
        'ukbota.hunter_accumulator.weekend26' => ['UKBOTA', 'Hunter', 'WE 2026'],
        'ukbota.hunter.roc26' => ['UKBOTA', 'Hunter', 'ROC 2026']
    ],
    'operations' => [
        [
            'name' => 'Sperrstelle Klingental',
            'route' => '/operation/sperrstelle-klingental',
            'location' => [
                ['Altenberg A', '47.343067/8.406719', ['B/HB-2501','B/HB-2503','B/HB-2504','B/HB-2505','B/HB-2506']],
            ],
            'area' => [
                ['point' => ['47.343677/8.400279','47.349260/8.423187','47.344607/8.425932','47.338384/8.404924']],
            ],
        ],
        [
            'name' => 'Sperrstelle Uetliberg',
            'route' => '/operation/sperrstelle-uetliberg',
            'location' => [
                ['Hohenstein A', '47.367437/8.475416', ['B/HB-2452','B/HB-2451','B/HB-2450','B/HB-2449','B/HB-2448'], ['CH-0121']],
                ['Hohenstein B', '47.360003/8.477942', ['B/HB-2447','B/HB-2446','B/HB-2445','B/HB-2444','B/HB-2443'], ['CH-0121']],
                ['Hohenstein C', '47.369802/8.466560', ['B/HB-2442','B/HB-2441','B/HB-2439','B/HB-2438','B/HB-2436']],
            ],
        ],
        [
            'name' => 'Sperrstelle Uitikon-Waldegg',
            'route' => '/operation/sperrstelle-uitikon-waldegg',
            'location' => [
                ['Buechhoger A', '47.375751/8.463684', ['B/HB-2393','B/HB-2394','B/HB-2397','B/HB-2398','B/HB-2399']],
                ['Buechhoger B', '47.373195/8.460003', ['B/HB-2400','B/HB-2401','B/HB-2402','B/HB-2403','B/HB-2404']],
                ['Buechhoger C', '47.373195/8.460003', ['B/HB-2406','B/HB-2407','B/HB-2408','B/HB-2409','B/HB-2410']],
                ['Buechhoger D', '47.373195/8.460003', ['B/HB-2411','B/HB-2412','B/HB-2413','B/HB-2414','B/HB-2417']],
                ['Buechhoger E', '47.373195/8.460003', ['B/HB-2418','B/HB-2419','B/HB-2435','B/HB-2437','B/HB-2440']],
            ],
        ],
        [
            'name' => 'Artilleriestellung Ringlikon',
            'route' => '/operation/artilleriestellung-ringlikon',
            'location' => [
                ['Ringlikon A', '47.356329/8.466548', ['B/HB-2456','B/HB-2457','B/HB-2458','B/HB-2459','B/HB-2460']],
                ['Ringlikon B', '47.356329/8.466548', ['B/HB-2461','B/HB-2462','B/HB-2464','B/HB-2465','B/HB-2466']],
                ['Ringlikon C', '47.356329/8.466548', ['B/HB-2467','B/HB-2468','B/HB-2469','B/HB-2470','B/HB-2471']],
                ['Ringlikon D', '47.356329/8.466548', ['B/HB-2472','B/HB-2473','B/HB-2474','B/HB-2521','B/HB-2530']],
                ['Ringlikon E', '47.356329/8.466548', ['B/HB-2531','B/HB-2532','B/HB-2535','B/HB-2536','B/HB-2537']], // Landikon
            ],
        ],
        [
            'name' => 'Artilleriestellung Hauwald',
            'route' => '/operation/artilleriestellung-hauwald',
            'location' => [
                ['Hauwald A', '47.420613/8.222465', ['B/HB-2088','B/HB-2089','B/HB-2090','B/HB-2091']],
                ['Hauwald B', '47.420613/8.222465', ['B/HB-2092','B/HB-2093','B/HB-2094']],
            ],
        ],
        [
            'name' => 'Sperrstelle Heitersberg',
            'route' => '/operation/sperrstelle-heitersberg',
            'location' => [
                ['Hasenberg A', '47.373226/8.363855', ['B/HB-2561','B/HB-2571','B/HB-2572','B/HB-2573','B/HB-2574']],
                ['Heitersberg A', '47.388421/8.362430', ['B/HB-2575','B/HB-2576','B/HB-2578','B/HB-2579','B/HB-2580']],
                ['Heitersberg B', '47.388421/8.362430', ['B/HB-2577','B/HB-2581','B/HB-2582','B/HB-2583','B/HB-2584']],
                ['Heitersberg C', '47.388421/8.362430', ['B/HB-2585','B/HB-2586','B/HB-2587','B/HB-2588','B/HB-2589']],
                ['Heitersberg D', '47.415369/8.342839', ['B/HB-2642','B/HB-2643','B/HB-2646','B/HB-2647','B/HB-2648']]
            ],
        ],
        [
            'name' => 'Sperrstelle Rudolfstetten',
            'route' => '/operation/sperrstelle-rudolfstetten',
            'location' => [                
                ['Friedlisberg A', '47.353282/8.382933', ['B/HB-2539','B/HB-2540','B/HB-2541','B/HB-2542','B/HB-2543']],
                ['Friedlisberg B', '47.353282/8.382933', ['B/HB-2544','B/HB-2545','B/HB-2546','B/HB-2547','B/HB-2548']],
                ['Hasenberg B', '47.373226/8.363855', ['B/HB-2566','B/HB-2567','B/HB-2568','B/HB-2569','B/HB-2570']],
                ['Rudolfstetten A', '47.370109/8.378431', ['B/HB-2549','B/HB-2550','B/HB-2555','B/HB-2556','B/HB-2557']],
                ['Rudolfstetten B', '47.370109/8.378431', ['B/HB-2558','B/HB-2559','B/HB-2560','B/HB-2564','B/HB-2565']],
            ],
        ],
        [
            'name' => 'Artilleriestellung Hegnau',
            'route' => '/operation/artilleriestellung-hegnau',
            'location' => [
                ['Hegnau A', '47.365619/8.326084', ['B/HB-2099','B/HB-2100','B/HB-2101','B/HB-2102'], ['CH-0120']],
                ['Hegnau B', '47.365619/8.326084', ['B/HB-2103','B/HB-2104','B/HB-2105','B/HB-2106'], ['CH-0120']],
                ['Hegnau C', '47.365619/8.326084', ['B/HB-2107','B/HB-2108','B/HB-2109','B/HB-2110'], ['CH-0120']],
            ],
        ],
        [
            'name' => 'Artilleriestellung Hausen',
            'route' => '/operation/artilleriestellung-hausen',
            'location' => [
                ['Hausen A(rt)', '47.456924/8.220297', ['B/HB-2074','B/HB-2075','B/HB-2076','B/HB-2077']],
                ['Hausen B', '47.456924/8.220297', ['B/HB-2078','B/HB-2079','B/HB-2080']],
            ],
        ],
        [
            'name' => 'Artilleriestellung Rammernwald',
            'route' => '/artilleriestellung-rammernwald',
            'location' => [
                ['Rammernwald A', '47.361295/8.445012', ['B/HB-2481','B/HB-2482','B/HB-2483','B/HB-2484','B/HB-2485']],
                ['Rammernwald B', '47.361295/8.445012', ['B/HB-2486','B/HB-2487','B/HB-2488','B/HB-2489','B/HB-2490']],
                ['Rammernwald C', '47.361295/8.445012', ['B/HB-2491','B/HB-2492','B/HB-2493','B/HB-2494','B/HB-2495']],
                ['Rammernwald D', '47.361295/8.445012', ['B/HB-2475','B/HB-2476','B/HB-2477','B/HB-2478','B/HB-2479']],
            ],
        ],
        [
            'name' => 'Sperrstelle Urdorf',
            'route' => '/operation/sperrstelle-urdorf',
            'location' => [
                ['Fadmatt A', '47.382379/8.427638', ['B/HB-0125','B/HB-0133','B/HB-0413','B/HB-0427','B/HB-0696']],
                ['Fadmatt B', '47.382379/8.427638', ['B/HB-2384','B/HB-2429','B/HB-2432','B/HB-2433','B/HB-2434']],
                ['Fadmatt C', '47.382379/8.427638', ['B/HB-1216','B/HB-1288','B/HB-1315','B/HB-1351','B/HB-2623']],
                ['Rebberg A', '47.376623/8.420492', ['B/HB-2613','B/HB-2614','B/HB-2615','B/HB-2621','B/HB-2622']],
                ['Rebberg B', '47.376623/8.420492', ['B/HB-2616','B/HB-2617','B/HB-2618','B/HB-2619','B/HB-2620']],                
                ['Schlatt A', '47.381909/8.447130', ['B/HB-2386','B/HB-2387','B/HB-2415','B/HB-2416','B/HB-2420']],
                ['Schlatt B', '47.381909/8.447130', ['B/HB-2421','B/HB-2422','B/HB-2424','B/HB-2425','B/HB-2426']],
                ['Schlatt C', '47.381909/8.447130', ['B/HB-2427','B/HB-2428','B/HB-2430','B/HB-2431']],
                ['Schlatt D', '47.381909/8.447130', ['B/HB-4793','B/HB-4794','B/HB-4796','B/HB-4797','B/HB-4798']],
            ],
        ],
        [
            'name' => 'Sperrstelle Dietikon',
            'route' => '/operation/sperrstelle-dietikon',
            'location' => [
                ['Guggenbühl A', '47.396806/8.405491', ['B/HB-2624','B/HB-2625','B/HB-2626','B/HB-2627']],
                ['Guggenbühl B', '47.394495/8.407204', ['B/HB-2628','B/HB-2630','B/HB-2631','B/HB-2632']],
                ['Guggenbühl C', '47.394495/8.407204', ['B/HB-2633','B/HB-2634','B/HB-2635','B/HB-2636']],
            ],
        ],
        [
            'name' => 'Sperrstelle Limmatufer',
            'route' => '/operation/sperrstelle-limmatufer',
            'location' => [
                ['Zelgli A', '47.403959/8.442111', ['B/HB-2374','B/HB-2375','B/HB-2376','B/HB-2377','B/HB-2378']],
                ['Zelgli B', '47.403959/8.442111', ['B/HB-2379','B/HB-2380','B/HB-2381','B/HB-2382','B/HB-2383']]
            ],
        ],
        [
            'name' => 'Sperrstelle Reppischhof',
            'route' => '/operation/sperrstelle-reppischhof',
            'location' => [
                ['Reppischhof A', '47.389180/8.391664', ['B/HB-2591','B/HB-2592','B/HB-2593','B/HB-2594','B/HB-2595']],
                ['Reppischhof B', '47.389180/8.391664', ['B/HB-2596','B/HB-2597','B/HB-2598','B/HB-2599','B/HB-2600']],
                ['Reppischhof C', '47.389180/8.391664', ['B/HB-2601','B/HB-2602','B/HB-2603','B/HB-2604','B/HB-2605']],
            ],
        ],
        [
            'name' => 'Sperrstelle Reppischtal',
            'route' => '/operation/sperrstelle-reppischtal',
            'location' => [
                ['Unterreppischtal A', '47.376479/8.397013', ['B/HB-2606','B/HB-2607','B/HB-2608','B/HB-2609','B/HB-2610']],
                ['Unterreppischtal B', '47.376479/8.397013', ['B/HB-2612','B/HB-2551','B/HB-2552','B/HB-2553','B/HB-2554']],
                ['Unterreppischtal C', '47.376479/8.397013', ['B/HB-4783','B/HB-4799','B/HB-4800']],
                ['Oberreppischtal A', '47.355614/8.428936', ['B/HB-2496','B/HB-2497','B/HB-2499','B/HB-2500']],
                ['Oberreppischtal B', '47.355614/8.428936', ['B/HB-2516','B/HB-2518','B/HB-2519','B/HB-2520']],
                ['Stierliberg A', '47.354725/8.418172', ['B/HB-2507','B/HB-2508','B/HB-2509','B/HB-2510','B/HB-2511']],
                ['Stierliberg B', '47.354725/8.418172', ['B/HB-2512','B/HB-2513','B/HB-2514','B/HB-2515','B/HB-2517']],
            ],
        ],
        [
            'name' => 'Sperrstelle Wollishofen',
            'route' => '/operation/sperrstelle-wollishofen',
            'location' => [
                ['Entlisberg A', '47.335018/8.524860', ['B/HB-2335','B/HB-2336','B/HB-2337','B/HB-2338','B/HB-2339']],
                ['Entlisberg B', '47.335018/8.524860', ['B/HB-2340','B/HB-2343','B/HB-2346','B/HB-2347','B/HB-2348']],
            ],
        ],
        [
            'name' => 'Sperrstelle Schlieren',
            'route' => '/operation/sperrstelle-schlieren',
            'location' => [
                ['Schlierenberg A', '47.397125/8.457378', ['B/HB-2388','B/HB-2389','B/HB-2390','B/HB-2391','B/HB-2392']],
            ],
        ],
        [
            'name' => 'Sperrstelle Baden-Münslishausen',
            'route' => '/operation/sperrstelle-baden-muenzlishausen',
            'location' => [
                ['Münzlishausen A', '47.474515/8.278969', ['B/HB-2029','B/HB-2030','B/HB-2031','B/HB-2032','B/HB-2033']],
                ['Münzlishausen B', '47.474515/8.278969', ['B/HB-2034','B/HB-2035','B/HB-2036','B/HB-2037','B/HB-2038']],
                ['Meierhof A', '47.466436/8.290739', ['B/HB-2001','B/HB-2002','B/HB-2003','B/HB-2004','B/HB-2005']],
                ['Meierhof B', '47.466436/8.290739', ['B/HB-2007','B/HB-2008','B/HB-2009','B/HB-2010','B/HB-2011']],
                ['Meierhof C', '47.466436/8.290739', ['B/HB-2012','B/HB-2013','B/HB-2025','B/HB-2026','B/HB-2027']],
            ],
            'area' => [
                ['point' => ['47.481650/8.267817','47.485710/8.292526','47.475385/8.294243','47.464709/8.309171','47.458674/8.280343','47.472600/8.281888']],
            ],
        ],
        [
            'name' => 'Sperrstelle Dättwil-Neuenhof',
            'route' => '/operation/sperrstelle-daettwil-neuenhof',
            'location' => [
                ['Baregg A', '47.459775/8.304609', ['B/HB-1988','B/HB-1989','B/HB-1990','B/HB-1991','B/HB-1993']],
                ['Baregg B', '47.459775/8.304609', ['B/HB-1994','B/HB-1995','B/HB-1996','B/HB-1997','B/HB-1998']],
                ['Baregg C', '47.459775/8.304609', ['B/HB-1999','B/HB-2000','B/HB-2006']],
                ['Sommerhalde A', '47.449017/8.285469', ['B/HB-2014','B/HB-2015','B/HB-2016','B/HB-2017']],
                ['Sommerhalde B', '47.449017/8.285469', ['B/HB-2018','B/HB-2019','B/HB-2020','B/HB-2021']],
                ['Rüsler A', '47.442544/8.315769', ['B/HB-1967','B/HB-1968','B/HB-1969','B/HB-1970','B/HB-1971']],
                ['Rüsler B', '47.442544/8.315769', ['B/HB-1972','B/HB-1973','B/HB-1974','B/HB-1975','B/HB-1976']],
                ['Rüsler C', '47.442544/8.315769', ['B/HB-1977','B/HB-1978','B/HB-1979','B/HB-1980','B/HB-1981']],
                ['Rüsler D', '47.442544/8.315769', ['B/HB-1982','B/HB-1983','B/HB-1984','B/HB-1985']],
            ],
        ],
        [
            'name' => 'Sperrstelle Bürersteig',
            'route' => '/operation/sperrstelle-buerersteig',
            'location' => [
                ['Bürersteig A', '47.528911/8.158488', ['B/HB-0082','B/HB-1886','B/HB-1909','B/HB-1910','B/HB-1911'], ['P/CH-0002']],
                ['Bürersteig B', '47.528911/8.158488', ['B/HB-1912','B/HB-1913','B/HB-1914','B/HB-1915','B/HB-1916'], ['P/CH-0002']],
            ],
        ],
        [
            'name' => 'Sperrstelle Villigen',
            'route' => '/operation/sperrstelle-villigen',
            'location' => [
                ['Geissberg A', '47.526170/8.217598', ['B/HB-1869','B/HB-1878','B/HB-1879','B/HB-1880','B/HB-1881'], ['P/CH-0002']],
                ['Geissberg B', '47.526170/8.217598', ['B/HB-1882','B/HB-1883','B/HB-1884','B/HB-1885'], ['P/CH-0002']],
            ],
        ],
        [
            'name' => 'Sperrstelle Rein-Roost',
            'route' => '/operation/sperrstelle-rein-roost',
            'location' => [
                ['Wasserschloss A', '47.509989/8.228765', ['B/HB-0080','B/HB-1865','B/HB-1870','B/HB-1871','B/HB-1872'], ['P/CH-0101']],
                ['Wasserschloss B', '47.509989/8.228765', ['B/HB-1873','B/HB-1874','B/HB-1875','B/HB-1876','B/HB-1877'], ['P/CH-0101']],
                ['Wasserschloss C', '47.509989/8.228765', ['B/HB-1890','B/HB-1891','B/HB-1905','B/HB-1906','B/HB-1907'], ['P/CH-0101']],
            ],
        ],
        [
            'name' => 'Sperrstelle Gebenstorf',
            'route' => '/operation/sperrstelle-gebenstorf',
            'location' => [
                ['Wasserschloss D', '47.489634/8.242487', ['B/HB-2039','B/HB-2040','B/HB-2041','B/HB-2043','B/HB-2044']],
                ['Wasserschloss E', '47.489634/8.242487', ['B/HB-2045','B/HB-2046','B/HB-2047','B/HB-2048','B/HB-2049']],
                ['Rebberg A', '47.472468/8.238562', ['B/HB-2057','B/HB-2058','B/HB-2059','B/HB-2060','B/HB-2061']],
                ['Rebberg B', '47.472468/8.238562', ['B/HB-2068','B/HB-2069','B/HB-2070','B/HB-2071','B/HB-2072']],
                // ['A', '47.472468/8.238562', ['B/HB-','B/HB-','B/HB-','B/HB-','B/HB-']],
                // ['B', '47.472468/8.238562', ['B/HB-','B/HB-','B/HB-','B/HB-','B/HB-']],
            ],
        ],
        [
            'name' => 'Sperrstelle Bruggerberg',
            'route' => '/operation/sperrstelle-bruggerberg',
            'location' => [
                ['Bruggerberg A', '47.507831/8.211612', ['B/HB-1892','B/HB-1893','B/HB-1894','B/HB-1895','B/HB-1908'], ['P/CH-0101']],
                ['Bruggerberg B', '47.494436/8.192612', ['B/HB-1896','B/HB-1900','B/HB-1901','B/HB-1902','B/HB-1903'], ['P/CH-0101']],
            ],
        ],
        [
            'name' => 'Sperrstelle Zeihen-Eichwald',
            'route' => '/operation/sperrstelle-zeihen-eichwald',
            'location' => [
                ['Wideregg A', '47.477289/8.099981', ['B/HB-1948','B/HB-1949','B/HB-1950','B/HB-1951','B/HB-1952'], ['P/CH-0002']],
                ['Eichwald A', '47.477289/8.099981', ['B/HB-1953','B/HB-1954','B/HB-1955','B/HB-1956','B/HB-1957'], ['P/CH-0002']],
                ['Eichwald B', '47.467456/8.097200', ['B/HB-1958','B/HB-1959','B/HB-1960','B/HB-1961','B/HB-1965'], ['P/CH-0002']],
            ],
        ],
        [
            'name' => 'Sperrstelle Frick',
            'route' => '/operation/sperrstelle-frick',
            'location' => [
                ['Frickerberg A', '47.507989/8.040488', ['B/HB-2221','B/HB-2222','B/HB-2223','B/HB-2224','B/HB-2225'], ['P/CH-0002']],
                ['Frickerberg B', '47.507989/8.040488', ['B/HB-2226','B/HB-2227','B/HB-2228','B/HB-2229','B/HB-2230'], ['P/CH-0002']],
                ['Frickerberg C', '47.507989/8.040488', ['B/HB-2231','B/HB-2232','B/HB-2287','B/HB-2288','B/HB-2289'], ['P/CH-0002']],
                ['Kornberg A', '47.500848/8.034195', ['B/HB-2290','B/HB-2291','B/HB-2292','B/HB-2293','B/HB-2294'], ['P/CH-0002']],
                ['Kornberg B', '47.513067/8.020769', ['B/HB-2233','B/HB-2234','B/HB-2235','B/HB-2236','B/HB-2237'], ['P/CH-0002']],
                ['Ittenthal A', '47.507305/8.059998', ['B/HB-2211','B/HB-2212','B/HB-2213','B/HB-2214','B/HB-2215'], ['P/CH-0002']],
                ['Ittenthal B', '47.507305/8.059998', ['B/HB-2216','B/HB-2217','B/HB-2218','B/HB-2219','B/HB-2220'], ['P/CH-0002']],
                ['Moos A', '47.513279/8.006838', ['B/HB-2238','B/HB-2241','B/HB-2242','B/HB-2243','B/HB-2244'], ['P/CH-0002']],
                ['Moos B', '47.513279/8.006838', ['B/HB-2245','B/HB-2246','B/HB-2247','B/HB-2248','B/HB-2249'], ['P/CH-0002']],
                ['Saengeliboden A', '47.513279/8.006838', ['B/HB-2250','B/HB-2251','B/HB-2252','B/HB-2253','B/HB-2254'], ['P/CH-0002']],
                ['Saengeliboden B', '47.513279/8.006838', ['B/HB-2255','B/HB-2256','B/HB-2257','B/HB-2258','B/HB-2259'], ['P/CH-0002']],
                ['Saengeliboden C', '47.513279/8.006838', ['B/HB-2260','B/HB-2261','B/HB-2262','B/HB-2263','B/HB-2264'], ['P/CH-0002']],
                ['Saengeliboden D', '47.510157/7.988614', ['B/HB-2265','B/HB-2266','B/HB-2267','B/HB-2268','B/HB-2269'], ['P/CH-0002']],
                ['Saengeliboden E', '47.510157/7.988614', ['B/HB-2270','B/HB-2271','B/HB-2272','B/HB-2273','B/HB-2274'], ['P/CH-0002']],
                ['Wolberg A', '47.510157/7.988614', ['B/HB-2275','B/HB-2276','B/HB-2277','B/HB-2278','B/HB-2279'], ['P/CH-0002']],
                ['Wolberg B', '47.510157/7.988614', ['B/HB-2280','B/HB-2281','B/HB-2282','B/HB-2283','B/HB-2284'], ['P/CH-0002']],
            ],
        ],
        /*
        [
            'name' => 'Dimmis',
            'undisclosed' => true,
            'set' => [
                ['47.520158/8.117721', ['B/HB-1917','B/HB-1923','B/HB-1924','B/HB-1925','B/HB-1927']],
                ['47.525450/8.099256', ['B/HB-1918','B/HB-1920','B/HB-1921','B/HB-1922','B/HB-1926']],
            ],
        ],
        */
        [
            'name' => 'Sperrstelle Stallikon',
            'route' => '/operation/sperrstelle-stallikon',
            'location' => [
                ['Gamlikon A', '47.316388/8.489437', ['B/HB-2523','B/HB-2524','B/HB-2525','B/HB-2527','B/HB-2528']],
            ],
        ],
        [
            'name' => 'Sperrstelle Tuggen',
            'route' => '/operation/sperrstelle-tuggen',
            'location' => [
                ['Tuggen A', '47.212838/8.964947', ['B/HB-0022','B/HB-3293','B/HB-3294','B/HB-3295','B/HB-3301']],
                ['Tuggen B', '47.212838/8.964947', ['B/HB-3302','B/HB-3303','B/HB-3304','B/HB-3305','B/HB-3306']],
                ['Tuggen C', '47.207685/8.945065', ['B/HB-3291','B/HB-3292','B/HB-3307','B/HB-3308','B/HB-3309']],
                ['Tuggen D', '47.205532/8.932305', ['B/HB-3310','B/HB-3311','B/HB-3312','B/HB-3313','B/HB-3314']],
                // ['Tuggen ', [], ['B/HB-','B/HB-','B/HB-','B/HB-','B/HB-']],
            ],
        ],
        [
            'name' => 'Sperrstelle Zweidlen-Lätten',
            'route' => '/operation/sperrstelle-zweidlen-laetten',
            'location' => [
                ['Zweidlen A', '47.566074/8.468364', ['B/HB-4551','B/HB-4552','B/HB-4553','B/HB-4554','B/HB-4555']],
                ['Zweidlen B', '47.566074/8.468364', ['B/HB-4556','B/HB-4557','B/HB-4558','B/HB-4559','B/HB-4560']],
                ['Zweidlen C', '47.566074/8.468364', ['B/HB-4561','B/HB-2708','B/HB-2709','B/HB-2710','B/HB-2747']],
            ],
        ],
        [
            'name' => 'Sperrstelle Stadel',
            'route' => '/operation/sperrstelle-stadel',
            'location' => [
                ['Stadel A', '47.533073/8.473835', ['B/HB-2686','B/HB-2687','B/HB-2688','B/HB-2689','B/HB-2850']],
                ['Stadel B', '47.533073/8.473835', ['B/HB-2851','B/HB-2852','B/HB-4547','B/HB-4548','B/HB-4549']],
                ['Stadel C', '47.522761/8.492808', ['B/HB-4562','B/HB-4563','B/HB-4802','B/HB-4803','B/HB-4804'], ['P/CH-0137']],
            ],
        ],
        [
            'name' => 'Sperrstelle Schinznach',
            'route' => '/operation/sperrstelle-schinznach',
            'location' => [
                ['Habsburg', '47.449797/8.176192', ['B/HB-2081','B/HB-2082','B/HB-2083','B/HB-2084']],
                ['Scherz', '47.449797/8.176192', ['B/HB-2085','B/HB-2086','B/HB-2087']],
            ],
        ],
        [
            'name' => 'Sperrstelle Stelli-Gallenkirch',
            'route' => 'sperrstelle-stelli-gallenkirch',
            'location' => [
                ['Leisacker', '47.485835/8.158412', ['B/HB-1939','B/HB-1940','B/HB-1941','B/HB-1942','B/HB-1943'],['P/CH-0002']],
                ['Riemen A', '47.481623/8.127737', ['B/HB-1931','B/HB-1932','B/HB-1933','B/HB-1934','B/HB-1936'],['P/CH-0002']],
                ['Riemen B', '47.481623/8.127737', ['B/HB-1944','B/HB-1945','B/HB-1946','B/HB-4540','B/HB-4543'],['P/CH-0002']],
                ['Birch', '47.481392/8.145599', ['B/HB-1935','B/HB-4538','B/HB-4539','B/HB-4544','B/HB-4545'],['P/CH-0002']],
            ],
        ],
    ],
];

/* template
        [
            'name' => '',
            'route' => '',
            'location' => [
                ['', ['B/HB-','B/HB-','B/HB-','B/HB-','B/HB-']],
                ['', ['B/HB-','B/HB-','B/HB-','B/HB-','B/HB-']],
            ],
        ],
*/