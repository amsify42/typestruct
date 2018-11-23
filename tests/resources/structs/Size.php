<?php

namespace TestTS\resources\structs;

export typestruct Size {
	id: int(5),
	name: string(15),
	price: float(5.2),
	accessories: string[4]
}