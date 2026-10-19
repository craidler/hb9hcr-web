import json
import re
import os

class CtyFlatParser:
    @staticmethod
    def to_flat_json(file_path: str, output_path: str = None) -> str:
        if not os.path.exists(file_path):
            raise FileNotFoundError(f"The file {file_path} does not exist.")

        # This will hold our flattened prefix-to-country structure
        flat_map = {}
        
        current_country_data = None
        prefix_buffer = ""

        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            for line in f:
                # Check for country header row (no leading whitespace, contains colons)
                if line and not line.startswith(' ') and ':' in line:
                    
                    # Process and assign the buffered prefixes from the previous country
                    if current_country_data is not None:
                        CtyFlatParser._map_prefixes_to_country(flat_map, prefix_buffer, current_country_data)

                    parts = line.strip().split(':')
                    while len(parts) < 9:
                        parts.append('')

                    # Extract common metadata payload
                    current_country_data = {
                        'country': parts[0].strip(),
                        'cq_zone': int(parts[1].strip()) if parts[1].strip().isdigit() else 0,
                        'itu_zone': int(parts[2].strip()) if parts[2].strip().isdigit() else 0,
                        'continent': parts[3].strip(),
                        'latitude': float(parts[4].strip()) if parts[4].strip() else 0.0,
                        'longitude': float(parts[5].strip()) if parts[5].strip() else 0.0,
                        'gmt_offset': float(parts[6].strip()) if parts[6].strip() else 0.0,
                        'primary': parts[7].strip()
                    }
                    
                    prefix_buffer = ""
                else:
                    # Append continuing prefix fragments
                    prefix_buffer += line

            # Catch the final item group at the end of the file stream
            if current_country_data is not None:
                CtyFlatParser._map_prefixes_to_country(flat_map, prefix_buffer, current_country_data)

        # Export structure
        json_data = json.dumps(flat_map, indent=4, ensure_ascii=False)

        if output_path:
            with open(output_path, 'w', encoding='utf-8') as out_f:
                out_f.write(json_data)

        return json_data

    @staticmethod
    def _map_prefixes_to_country(flat_map: dict, buffer: str, country_data: dict) -> None:
        buffer = buffer.strip().rstrip(';')
        if not buffer:
            return

        raw_prefixes = buffer.split(',')
        for prefix in raw_prefixes:
            prefix = prefix.strip()
            if not prefix:
                continue

            # Clean amateur radio modifiers out (e.g. VE(3) -> VE, KH6/W -> KH6)
            prefix = re.split(r'[\(\[\{\<\=\/]', prefix)[0].strip().upper()

            if prefix:
                # Map the prefix variant as a direct root key in the dictionary
                flat_map[prefix] = country_data

# ==========================================
# Execution Example
# ==========================================
if __name__ == "__main__":
    try:
        input_file = 'cty.dat'
        output_file = 'dxcc.json'
        
        print("Parsing and flattening cty.dat into a prefix map...")
        CtyFlatParser.to_flat_json(input_file, output_file)
        print(f"Success! Map structure exported directly to {output_file}")

    except Exception as e:
        print(f"Error parsing file: {e}")
