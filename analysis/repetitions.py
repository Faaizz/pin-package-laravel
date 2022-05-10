import json

import matplotlib.pyplot as plt
import numpy as np

with open('repetitions.json') as f:
    repetitions = json.load(f)

plt.plot(range(0, len(repetitions)), repetitions)
plt.savefig('repetitions.png')

repetitionsArr = np.array(repetitions)

print("Minimum number of successive trials to get repetition: ", np.min(repetitionsArr))
print("Number of successive trials to get repetition that are < 100: ", repetitionsArr[repetitionsArr < 100].shape[0])
print("Average number of successive trials to get repetition: ", np.average(repetitionsArr))
print("Maximum number of successive trials to get repetition: ", np.max(repetitionsArr))
