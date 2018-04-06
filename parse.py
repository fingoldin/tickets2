import json
from pprint import pprint

with open('./data/12333_output.json') as file:
	data = json.load(file);

pprint(data);
