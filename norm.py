import numpy as np
import json
from scipy.stats import norm

niter = 1000000

data = json.loads(open("risk_one.json").read())
choices = []
probabilities = []
norms = []

php_test = json.loads(open("test_random.json").read())

for i in range(len(data["spinner"])):
    val = float(data["spinner"][i]["value"])
    prob = float(data["spinner"][i]["fraction"])
    choices.append(val)
    probabilities.append(prob)
    norms.append(norm.cdf(val, 180, 20) - norm.cdf(val - 1.0, 180, 20))

norms = np.array(norms) / np.sum(norms)
vals = np.random.choice(choices, niter, True, norms)
unique, counts = np.unique(vals, return_counts=True)

print("Python: " + str(counts))
print("PHP: " + str(php_test))

print(np.mean(np.abs(counts - php_test)))
