import csv
import json
import numpy as np

data = [ row for row in csv.reader(open("risky_gamble_percent.csv"), delimiter=',') ]

spinners = np.repeat(np.array(data)[1:,1:], 10, axis=1).astype(np.float) / 10
spinners[:,0:10] *= 10 / 11
spinners = np.insert(spinners, 0, spinners[:,0], axis=1)
print(spinners[0])

output = []
 

for spinner in spinners:
  output.append([])
  for i in range(len(spinner)):
    frac = float(spinner[i])
    output[-1].append({ "fraction": (frac), "value": 125 + i, "show": bool(i % 5 == 0) })

open("spinners3.json", "w").write(json.dumps(output))
