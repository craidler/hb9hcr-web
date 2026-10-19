import sys
import re
import pdfplumber
import pandas as pd

def lv95_to_wgs84(e_lv95, n_lv95):
    """
    Konvertiert Schweizer Landeskoordinaten (LV95 / CH1903+) 
    in globale WGS84-Dezimalgrad (Breitengrad/Längengrad).
    """
    # 1. Hilfsgrössen berechnen (Differenz zum Bezugspunkt Bern)
    y = (e_lv95 - 2600000) / 1000000
    x = (n_lv95 - 1200000) / 1000000

    # 2. Projektionsformeln in Längengrad (lambda) und Breitengrad (phi) im Sexagesimalsystem
    # HIER WURDEN DIE POTENZEN KORRIGIERT:
    lambda_prime = 2.6779094 + 4.728982 * y + 0.791484 * y * x + 0.10141 * y * (x**2) - 0.04492 * (y**2) * x
    phi_prime = 16.9023892 + 3.238217 * x - 0.270973 * (y**2) - 0.002528 * (y**2) - 0.0447 * (y**2) * x - 0.0140 * (x**3)

    # 3. Umrechnung in Dezimalgrad
    lon = lambda_prime * 100 / 36
    lat = phi_prime * 100 / 36
    
    return round(lat, 5), round(lon, 5)

def parse_adab_to_csv(pdf_path, output_csv_path):
    print(f"Starte Extraktion aus: {pdf_path}...")
    
    # [ \t]* fängt mögliche Leerzeichen zwischen dem A und den Zahlen ab
    id_pattern = re.compile(r'[A-Z]{2}[_\s]([AFT][ \t]*\d{5})(?![ \t]*+\d{2}\b)')
    flurname_pattern = re.compile(r'(Flurname|Toponyme)[ \t]*(.*)')
    coords_pattern = re.compile(r'(Koordinaten|Coordonnées)[ \t]*(\d{6,7})\s+(\d{6,7})')
    erstnutzung_pattern = re.compile(r'(Erste Nutzung|Type d\'ouvrage originel)[ \t]*(.*)')
    
    extracted_data = []
    
    with pdfplumber.open(pdf_path) as pdf:
        total_pages = len(pdf.pages)
        print(f"Das PDF hat {total_pages} Seiten. Verarbeite...")
        
        for page_num, page in enumerate(pdf.pages, 1):
            text = page.extract_text()
            if not text:
                continue
            
            obj_id_match = id_pattern.search(text)
            flurname_match = flurname_pattern.search(text)
            coords_match = coords_pattern.search(text)
            erstnutzung_match = erstnutzung_pattern.search(text)
            
            if obj_id_match and coords_match:
                raw_id = obj_id_match.group(1)
                formatted_id = raw_id.replace(" ", "")
                flurname = flurname_match.group(2).strip() if flurname_match else "Unknown"
                erstnutzung = erstnutzung_match.group(2).strip() if erstnutzung_match else "Unknown"
                
                e_raw = coords_match.group(2)
                n_raw = coords_match.group(3)
                e_lv95 = int(e_raw) + (2000000 if len(e_raw) < 7 else 0)
                n_lv95 = int(n_raw) + (1000000 if len(n_raw) < 7 else 0)
                
                # In WGS84 umrechnen für OpenStreetMap
                lat, lon = lv95_to_wgs84(e_lv95, n_lv95)
                
                extracted_data.append({
                    "Nummer": formatted_id,
                    "Objektname": flurname,
                    "Erstnutzung": erstnutzung,
                    "Koordinaten": f"{lat}, {lon}"
                })
            
            if page_num % 100 == 0 or page_num == total_pages:
                print(f"Fortschritt: {page_num}/{total_pages} Seiten analysiert.")
                
    if extracted_data:
        df = pd.DataFrame(extracted_data)
        df.to_csv(output_csv_path, index=False, sep=";", encoding="utf-8", header=False)
        print(f"\nErfolgreich beendet! {len(df)} Objekte wurden in '{output_csv_path}' gespeichert.")
    else:
        print("\nEs wurden keine Daten extrahiert. Bitte Regex/Struktur prüfen.")


if __name__ == "__main__":
    # Standardwert setzen, falls kein Argument übergeben wurde
    kanton_prefix = "AG"
    
    # Überprüfen, ob ein Argument übergeben wurde (sys.argv[0] ist der Skriptname selbst)
    if len(sys.argv) > 1:
        kanton_prefix = sys.argv[1]
        
    input_pdf = f"{kanton_prefix}_Public.pdf"
    output_csv = f"{kanton_prefix.lower()}.csv"
    
    parse_adab_to_csv(input_pdf, output_csv)