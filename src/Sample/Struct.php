<?php

namespace Amsify42\TypeStruct\Sample;

use App\User;

export typestruct Struct {
	name: string,
	email: string,
	id: int,
	address: {
		door: string,
		zip: int
	},
	items: [],
	user : User,
	someEl: {
		key1: string,
		key2: int,
		key12: array,
		records: App\Record[],
		someChild: {
			key3: boolean,
			key4: float,
			someAgainChild: {
				key5: string,
				key6: float,
				key56: boolean[]
			}
		}
	}
}