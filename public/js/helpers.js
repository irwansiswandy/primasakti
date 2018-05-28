function findWithAttribute(array, attribute, value) {
	for (i=0; i<array.length; i++) {
		if (array[i][attribute] === value) {
			return i;
		}
	}
	return -1;
}