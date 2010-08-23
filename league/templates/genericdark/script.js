var defOff= '#282855';
var defOn = '#333388';
var clrOff = '#004400';
var clrOn  = '#006600';
var admOff = '#550000';
var admOn  = '#770000';

function butRoll (obj, theme, onoff){
	if (onoff){
		if (theme=='clrbut')
			obj.style.backgroundColor = clrOn;
		else if (theme=='admbut')
			obj.style.backgroundColor = admOn;
		else
			obj.style.backgroundColor = defOn;
	}else{
		if (theme=='clrbut')
			obj.style.backgroundColor = clrOff;
		else if (theme=='admbut')
			obj.style.backgroundColor = admOff;
		else
			obj.style.backgroundColor = defOff;
	}
}

