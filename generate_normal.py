import numpy as np

test = np.minimum(215, np.maximum(145, np.random.normal(180, 20, 4000).astype(np.int)))
train = np.minimum(215, np.maximum(145, np.random.normal(180, 20, (3, 30)).astype(np.int)))

np.savetxt("normal_180_20.csv", test, fmt='%d')
np.savetxt("normal_180_20_train.csv", train, fmt='%d', delimiter=',')
