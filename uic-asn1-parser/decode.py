import asn1tools
import base64
import json
import os
import sys

schema_map = {
    '13': 'asn1/uicRailTicketData_v1.3.4.asn',
    '03': 'asn1/uicRailTicketData_v3.0.3.asn',
}

version = sys.argv[1]
data = base64.b64decode(sys.argv[2])

asn1_schema = os.path.dirname(__file__) + '/' + schema_map[version]
compiler = asn1tools.compile_files(asn1_schema, 'uper')

decoded_data = compiler.decode('UicRailTicketData', data)

print(json.dumps(decoded_data))
