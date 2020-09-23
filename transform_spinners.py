import csv
import json
import numpy as np

data = [ row for row in csv.reader(open("risky_gambles.csv"), delimiter=',') ]

spinners = np.repeat(np.array(data)[1:,2:], 10, axis=1).astype(np.float) / 10
spinners[:,0:10] *= 10 / 11
spinners = np.insert(spinners, 0, spinners[:,0], axis=1)
spinners = spinners / np.resize(np.sum(spinners, axis=1), (spinners.shape[0], 1))

output = []
 

for spinner in spinners:
  output.append([])
  for i in range(len(spinner)):
    frac = float(spinner[i])
    output[-1].append({ "fraction": (frac), "value": 120 + i, "show": bool(i % 10 == 0) })

open("spinners4.json", "w").write(json.dumps(output))
