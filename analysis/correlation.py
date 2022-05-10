import json

import numpy as np

with open('correlation.json') as f:
    correlation = json.load(f)

correlationArr = np.array(correlation, dtype='uint32')

print(np.corrcoef(correlationArr))
