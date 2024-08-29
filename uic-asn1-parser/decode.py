import asn1tools
import base64
import json
import os
import sys
from pprint import pprint

schema_map = {
    '13': 'asn1/uicRailTicketData_v1.3.4.asn',
    '03': 'asn1/uicRailTicketData_v3.0.3.asn',
}

# Define a custom JSON encoder to handle bytes objects
class BytesToStringJSONEncoder(json.JSONEncoder):
    def default(self, obj):
        if isinstance(obj, bytes):
            # Convert bytes to UTF-8 string
            try:
                return obj.decode('utf-8')
            except UnicodeDecodeError:
                # Fallback: encode as base64 string if decoding fails
                return base64.b64encode(obj).decode('utf-8')
        # For all other types, use the default serialization
        return super().default(obj)

version = sys.argv[1]
data = base64.b64decode(sys.argv[2])

asn1_schema = os.path.dirname(__file__) + '/' + schema_map[version]
compiler = asn1tools.compile_files(asn1_schema, 'uper')

decoded_data = compiler.decode('UicRailTicketData', data)

print(json.dumps(decoded_data, cls=BytesToStringJSONEncoder, indent=4))
