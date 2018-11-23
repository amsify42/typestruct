<?php

namespace TestTS\resources\structs;

export typestruct Size {
	id: int(2),
	name: string(10),
	price: float(5,2),
	accessories: string[4]
}